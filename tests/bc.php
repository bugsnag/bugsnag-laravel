<?php

if (class_exists(PHPUnit\Framework\TestCase::class) && !class_exists(PHPUnit_Framework_TestCase::class)) {
    class_alias(PHPUnit\Framework\TestCase::class, PHPUnit_Framework_TestCase::class);
}
