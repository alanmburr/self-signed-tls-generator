<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator;

require_once __DIR__."/../vendor/autoload.php";

class DistinguishedName
{
    public static string $country;
    public static string $province;
    public static string $locality;
    public static string $organization;
    public static string|false $orgUnit;
    public static string $commonName;
    public static string|false $email;

    /**
     * Create a Destinguished Name for creating an X509 certificate
     * @param string $country Name of the user's country, as two letters (NOT CHECKED FOR VALIDITY).
     * @param string $province Name of the user's province or state, spelled out in full.
     * @param string $locality Name of the user's locality, spelled out in full.
     * @param string $organization Name of the user's organization OR the company creating the certificate.
     * @param string|false $orgUnit Name of the organizational unit. Can be `false` for no value.
     * @param string $commonName A Fully-Qualified Domain Name (FDQN) (NOT CHECKED FOR VALIDITY).
     * @param string $email A valid email for the user (NOT CHECKED FOR VALIDITY).
     * @return void Class constructors must return `void`.
     */
    public function __construct(
        string $country,
        string $province,
        string $locality,
        string $organization,
        string|false $orgUnit,
        string $commonName,
        string|false $email
    )
    {
        self::$country = $country;
        self::$province = $province;
        self::$locality = $locality;
        self::$organization = $organization;
        self::$orgUnit = $orgUnit;
        self::$commonName = $commonName;
        self::$email = $email;
    }

    public function toArray(): array
    {
        return [
            "countryName" => self::$country,
            "stateOrProvinceName" => self::$province,
            "localityName" => self::$locality,
            "organizationName" => self::$organization,
            "organizationalUnitName" => self::$orgUnit,
            "commonName" => self::$commonName,
            "emailAddress" => self::$email,
        ];
    }
}

