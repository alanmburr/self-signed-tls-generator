<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator;

require_once __DIR__."/../vendor/autoload.php";

class CaInformation
{
    public static string $crtFilePath;
    public static string $keyFilePath;
    public static string $keyPasscodePLAINTEXT;

    /**
     * Information about the CA certificate.
     * @param string $crtFilePath Full path to the X509 CA certificate.
     * @param string $keyFilePath Full path the the key-file for the CA certificate.
     * @param string $keyPasscode The passcode to the key, AS <b>PAINTEXT</b>.
     */
    public function __construct(
        string $crtFilePath,
        string $keyFilePath,
        string $keyPasscode,
    )
    {
        self::$crtFilePath = $crtFilePath;
        self::$keyFilePath = $keyFilePath;
        self::$keyPasscodePLAINTEXT = $keyPasscode;
    }

    public function toArray(): array
    {
        return [
            'crt' => self::$crtFilePath,
            'key' => [
                self::$keyFilePath,
                self::$keyPasscodePLAINTEXT
            ],
        ];
    }

    public function toObject(): CaInformationEx
    {
        return new CaInformationEx($this);
    }
}

