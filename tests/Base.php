<?php

declare(strict_types=1);

namespace Alanmburr\SelfSignedTlsGenerator\Test;

class Base {
    protected $callerName;

    public function __construct()
    {
        $this->setUp();
    }

    public function assertEquals(mixed $expected, mixed $actual, ?string $name = null): bool
    {

        \printf("Running test from {$this->getClass()}::{$this->callerName} : ");
        \ob_start();
        \var_dump($expected == $actual);
        printf("%s", trim(\ob_get_clean()));
        if (!\is_null($name)) { print_r(" // Comment: $name"); }
        \printf("\r\n");
        return $actual == $expected;
    }

    public function assertSame(mixed $expected, mixed $actual, ?string $name = null): bool
    {
        \printf("Running test from {$this->getClass()}::{$this->callerName} : ");
        \ob_start();
        \var_dump($expected === $actual);
        printf("%s", trim(\ob_get_clean()));
        if (!\is_null($name)) { print_r(" // Comment: $name"); }
        \printf("\r\n");
        return $actual === $expected;
    }

    public function setUp(): void
    {
        // Empty because we don't have any setup to do in the backport.
    }

    public function caller(string $caller): Base
    {
        $this->callerName = $caller;
        return $this;
    }

    protected function getClass(): string
    {
        return \get_class($this);
    }
}
