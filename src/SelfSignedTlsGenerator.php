<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator;

require_once __DIR__."/../vendor/autoload.php";

use margusk\OpenSSL\Wrapper\Proxy as SelfSignedTlsGenerator_OpenSSLProxy;
use Ramsey\Uuid\Uuid as SelfSignedTlsGenerator_Uuid;

class SelfSignedTlsGenerator
{
    private static SelfSignedTlsGenerator_OpenSSLProxy $proxy;
    private static bool $needsUniqueOpenSslCnfFilename = false;
    private static function isNull(mixed $item): bool {return \is_null($item);}

    public static DistinguishedName $dn;
    public string|null $openSslCnfString;
    public string|null $temporaryOpenSslCnfFilename;
    public string $vendorOpenSslConfigFilename = __DIR__ . "/VendorConfig.cnf";
    public string $vendorOpenSslConfigEndUserReqSectionName = "v3_req";
    public bool $debug = false;

    /**
     * Generate a Self-Signed TLS certificate using a pre-existing CA.
     * Debug options are available. Set the `$debug` member of this class to `true` for debugging.
     * @param \Alanmburr\SelfSignedTlsGenerator\DistinguishedName $dn A complete DN constructor.
     */
    public function __construct(
        DistinguishedName $dn,
        ?string $openSslCnfString = null,
        ?string $temporaryOpenSslCnfFilename = null,
        ?string $vendorOpenSslConfigFilename = null,
    )
    {
        self::$dn = $dn;
        self::$proxy = new SelfSignedTlsGenerator_OpenSSLProxy();

        if (!self::isNull($openSslCnfString))
        {
            $this->openSslCnfString = $openSslCnfString;
        }

        if (!self::isNull($temporaryOpenSslCnfFilename))
        {
            $this->temporaryOpenSslCnfFilename = $temporaryOpenSslCnfFilename;
        } else
        {
            self::$needsUniqueOpenSslCnfFilename = true;
        }

        if (!self::isNull($vendorOpenSslConfigFilename))
        {
            $this->vendorOpenSslConfigFilename = $vendorOpenSslConfigFilename;
        }
    }

    /**
     * Generate, in one function, an X509 certificate using a DN.
     * @param \Alanmburr\SelfSignedTlsGenerator\CaInformation $ca CA information class.
     * @param int $pkeyBits Number of bits to use for the private key. Defaults to 2048.
     * @param int $daysValid Any number of days between (logically) 1 and 3650 (10 years). Capped at 3650.
     */
    public function generateFromDN(CaInformation $ca, int $pkeyBits = 2048, int $daysValid = 3650): array
    {
        $this->checkForNeededUniqueCnfFilenameAndHandle();
        $this->loadOpenSslCnfStringIntoFile();

        $key = self::$proxy->pkeyNew([
            'private_key_type' => \OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => $pkeyBits,
            'digest_alg' => 'sha256',
            'config' => $this->temporaryOpenSslCnfFilename,
        ]);
        $this->checkForOpenSslErrors();

        $csr = self::$proxy->csrNew(
            self::$dn->toArray(),
            $key->value(),
            [
                'digest_alg' => 'sha256',
                'config' => $this->vendorOpenSslConfigFilename,
            ]
        );
        $this->checkForOpenSslErrors();

        $crt = self::$proxy->csrSign(
            $csr->value(),
            $ca::$crtFilePath,
            $ca->toObject()->key,
            ($daysValid <= 3650) ? $daysValid : 3650, // days of validity
            [
                'x509_extensions' => $this->vendorOpenSslConfigEndUserReqSectionName,
                'config' => $this->vendorOpenSslConfigFilename,
            ],
            0 // Do not serialize certificates.
        );
        $this->checkForOpenSslErrors();

        return [
            $key->value()->pkeyExport()->value(),
            $crt->value()->x509Export(!$this->debug)->value(),
        ];
    }

    private function checkForOpenSslErrors(): void
    {
        $openssl_error_report = \openssl_error_string();
        if (\is_string($openssl_error_report) && $openssl_error_report !== false)
        {
            if ($this->debug)
            {
                echo "(CODE E02) An OpenSSL Error has occurred: " . $openssl_error_report;
            }
            throw new \Error("OpenSSL Error. Enable Debug mode to see more info and CODE E02.");
        }
    }

    private function checkForNeededUniqueCnfFilenameAndHandle(): void
    {
        if (self::$needsUniqueOpenSslCnfFilename) {
            $this->temporaryOpenSslCnfFilename = SelfSignedTlsGenerator_Uuid::uuid5(
                SelfSignedTlsGenerator_Uuid::NAMESPACE_DNS,
                self::$dn::$commonName
            )->toString();
            if ($this->debug)
            {
                echo "(CODE I02) Temporary CNF file for OpenSSL: " . $this->temporaryOpenSslCnfFilename;
            }
            self::$needsUniqueOpenSslCnfFilename = false;
        }
    }

    /**
     * @throws \Error Will only throw if there's a Disk I/O error reported by the preprocessor.
     */
    private function loadOpenSslCnfStringIntoFile()
    {
        if (self::isNull($this->openSslCnfString))
        {
            // Load default config.
            $cn = (string) self::$dn::$commonName;
            $this->openSslCnfString = "[ req ]\ndistinguished_name = req_distinguished_name\nreq_extensions = v3_req\n[ req_distinguished_name ]\n[ v3_req ]\nbasicConstraints = critical, CA:FALSE\nkeyUsage = critical, nonRepudiation, digitalSignature, keyEncipherment\nextendedKeyUsage=serverAuth,clientAuth\nsubjectAltName = @alt_names\n[ alt_names ]\nDNS.1 = $cn\nDNS.2 = www.$cn\nDNS.3 = *.$cn";
            if ($this->debug)
            {
                echo "(CODE I01) Default OpenSSL CNF string will be loaded.";
            }
        }
        $fileOperation = \file_put_contents(
            $this->temporaryOpenSslCnfFilename,
            $this->openSslCnfString
        );
        if ($fileOperation === false)
        {
            // An error occurred while writing the config.
            if ($this->debug)
            {
                echo "(CODE E01) There was an unspecified error when attempting to write the OpenSSL CNF to disk.";
            }
            throw new \Error("File error while writing configurations. Enable Debug mode and see CODE E01.");
        }
    }
}

