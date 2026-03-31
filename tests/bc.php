<?php

if (class_exists(PHPUnit\Framework\TestCase::class) && !class_exists(PHPUnit_Framework_TestCase::class)) {
    class_alias(PHPUnit\Framework\TestCase::class, PHPUnit_Framework_TestCase::class);
}

// PHPUnit 10+ attributes - define stubs for older PHPUnit versions
// These allow the #[DataProvider], #[Before], #[After] attributes to be parsed
// without errors on PHPUnit 9.x which doesn't have these attribute classes
if (!class_exists('PHPUnit\Framework\Attributes\DataProvider')) {
    eval('
        namespace PHPUnit\Framework\Attributes;
        #[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
        final class DataProvider {
            public function __construct(string $methodName) {}
        }
    ');
}

if (!class_exists('PHPUnit\Framework\Attributes\Before')) {
    eval('
        namespace PHPUnit\Framework\Attributes;
        #[\Attribute(\Attribute::TARGET_METHOD)]
        final class Before {
            public function __construct() {}
        }
    ');
}

if (!class_exists('PHPUnit\Framework\Attributes\After')) {
    eval('
        namespace PHPUnit\Framework\Attributes;
        #[\Attribute(\Attribute::TARGET_METHOD)]
        final class After {
            public function __construct() {}
        }
    ');
}
