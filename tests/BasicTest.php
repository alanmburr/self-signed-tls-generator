<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator\Test;

require_once __DIR__."/Base.php";

class BasicTest extends Base
{
    public function setUp(): void
    {
        $this->testIfTrueIsTrue();
    }

    public function testIfTrueIsTrue(): void
    {
        $true = true;

        $this->caller(__FUNCTION__)->assertEquals(true, $true);
    }
}
