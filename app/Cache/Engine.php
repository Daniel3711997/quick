<?php

declare(strict_types=1);

namespace Quick\Cache;

use Phpfastcache\Helper\Psr16Adapter;
use Phpfastcache\Config\ConfigurationOption;

use function Quick\Plugin\getQuickPluginDirectory;

class Engine {
    public static ?Psr16Adapter $instance = null;

    public static function getInstance(): Psr16Adapter {
        if (null === self::$instance) {
            self::$instance = new Psr16Adapter(
                'Files',
                new ConfigurationOption([
                    'defaultTtl' => 3600,
                    'path' => getQuickPluginDirectory() . 'cache/quick',
                ])
            );
        }

        return self::$instance;
    }
}
