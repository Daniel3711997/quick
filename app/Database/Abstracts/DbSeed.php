<?php

namespace Quick\Database\Abstracts;

abstract class DbSeed {
    public static function runSeed() {
        throw new \Exception(
            __("Method 'runSeed' must be overridden", 'quick')
        );
    }

    public static function rollbackSeed() {
        throw new \Exception(
            __("Method 'rollbackSeed' must be overridden", 'quick')
        );
    }
}
