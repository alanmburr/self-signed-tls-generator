<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator\Test;

require_once __DIR__."/Base.php";
require_once __DIR__."/../src/CaInformation.php";
require_once __DIR__."/../src/CaInformationEx.php";

use \Alanmburr\SelfSignedTlsGenerator\CaInformation;
use \Alanmburr\SelfSignedTlsGenerator\CaInformationEx;

final class CaInformationTest extends Base
{
    protected $ca;

    public function setUp(): void
    {
        $this->ca = new CaInformation('./ca.crt', './ca.key', '123abc');

        $this->testClassConstructor();
        $this->testToArray();
        $this->testToObject();
    }

    public function testClassConstructor(): void
    {
        $this->caller(__FUNCTION__);
        $this->assertEquals('./ca.crt', $this->ca::$crtFilePath, "CA cert name");
        $this->assertEquals('./ca.key', $this->ca::$keyFilePath, "CA Key Name");
        $this->assertEquals('123abc', $this->ca::$keyPasscodePLAINTEXT, "CA passcode");
    }

    public function testToArray(): void
    {
        $this->assertSame(
            [
                'crt' => $this->ca::$crtFilePath,
                'key' => [
                    $this->ca::$keyFilePath,
                    $this->ca::$keyPasscodePLAINTEXT
                ],
            ],
            $this->ca->toArray(),
            "is the Array correct"
        );
    }

    public function testToObject(): void
    {
        $cax = new CaInformationEx($this->ca);

        $this->assertSame($cax, $this->ca->toObject(), "is the Object correct");
    }
}
