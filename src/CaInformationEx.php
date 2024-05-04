<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator;

require_once __DIR__."/../vendor/autoload.php";

class CaInformationEx
{
    public string $crt;
    public array $key;

    public function __construct(CaInformation $ca)
    {
        $this->crt = $ca::$crtFilePath;
        $this->key = [
            $ca::$keyFilePath,
            $ca::$keyPasscodePLAINTEXT,
        ];
    }
}

