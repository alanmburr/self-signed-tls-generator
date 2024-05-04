# SelfSignedTlsGenerator

*This library depends on a dev package. You may have to set your "minimim-stability" to "dev" in composer.json.*

1.  Install and update Composer dependencies… `composer install` then `composer update`

2.  Use…

    1.  First, create a DistinguishedName class…

> ```php
> $dn = new DistinguishedName("US", "Virginia", "Richmond", "org", false, "mynewawesometlsdomain.local", false);
> ```

2.  Create a new CaInformation class…

    1.  Inputs:

        1.  Full file path to the CA (the certificate must be in PEM
            format)

        2.  Full file path to the CA key

        3.  Password for the CA key (in plaintext, only)

> ```php
> $ca = new CaInformation(__DIR__."/ca.crt", __DIR__."/ca.key", "123abc");
> ```

3.  Create a new SelfSignedTlsGenerator class…

    1.  Inputs:

        1.  DistinguishedName (required)

        2.  OpenSSL Configuration (optional)

            1.  Leave null or pass in null to use the default config.

        3.  Per-domain OpenSSL Configuration (optional)

            1.  If left blank, the class will generate a UUID (gen 5)
                from the Common Name portion of the Distinguished Name.

        4.  Vendor OpenSSL Config Filename (optional)

            1.  Defaults to `VendorConfig.cnf`. Pass in a full path if
                you have a custom config.

> ```php
> $tlsgen = new SelfSignedTlsGenerator($dn);
> ```

4.  Run the generateFromDN function of the SelfSignedTlsGenerator class…

    1.  Inputs:

        1.  CaInformation (required)

        2.  Private Key bits (defaults to 2048)

        3.  Days valid (defaults to 3650 (10 years), capped at 10 years)

            1.  Capped at 10y because some browsers reject certificates
                that are valid for more than a decade. I haven't
                encountered this, and it seems like neither have other
                people, but it's a theoretical limitation to be aware of.

    2.  Returns:

        1.  An array of strings:

            1.  The Private Key for the new certificate

            2.  The Certificate contents.

> ```php
> $crt = $tlsgen->generateFromDN($ca);
> print_r($crt);
> ```
