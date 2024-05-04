<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator\Test;

require_once __DIR__."/Base.php";
require_once __DIR__."/../src/DestinguishedName.php";

use \Alanmburr\SelfSignedTlsGenerator\DistinguishedName;

final class DistinguishedNameTest extends Base {
    public function setUp(): void
    {
        $this->testFields();
    }

    public function testFields(): void
    {
        $dn = new DistinguishedName(
            "US", "Virginia", "Richmond",
            "TH", false, "alanburr.us.eu.org", false
        );
        $this->caller(__FUNCTION__);
        $this->assertEquals("US", $dn::$country, "country");
        $this->assertEquals("Virginia", $dn::$province, "state");
        $this->assertEquals("Richmond", $dn::$locality, "locality");
        $this->assertEquals("TH", $dn::$organization, "org");
        $this->assertEquals(false, $dn::$orgUnit, "org unit");
        $this->assertEquals("alanburr.us.eu.org", $dn::$commonName, "common name");
        $this->assertEquals(false, $dn::$email, "email");
    }
}
