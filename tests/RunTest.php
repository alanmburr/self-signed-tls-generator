<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator\Test;

if (\php_sapi_name() !== "cli") {die("please run from cli");}
if ($argc == 1) {die("type the CLASS NAME that you want to test after this script filename.");}

$testclassname = $argv[1];

echo "working dir: " .  __DIR__ . "\r\n\n";

require_once __DIR__.'/'.$testclassname.'.php';

$testclass = \class_alias("AlanMBurr\SelfSignedTlsGenerator\Test\\$testclassname", "\TestClass", true);

if ($testclass)
{
    $testclass = new \TestClass();

    $testclass->setUp();

    echo "Tests Complete";
}

// Tests should be done.
