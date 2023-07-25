<?php

declare(strict_types=1);

namespace Quick\Framework;

class Assets {
    private static $manifestJSON = null;
    private static string $manifestPath = QUICK_PLUGIN_DIRECTORY . 'build/manifest.json';

    public static function getAsset(string $asset): ?string {
        return self::getManifest()[$asset] ?? null;
    }

    public static function getManifest(): array {
        if (null !== self::$manifestJSON) {
            return self::$manifestJSON;
        }

        if (file_exists(self::$manifestPath)) {
            self::$manifestJSON = json_decode(
                file_get_contents(self::$manifestPath),
                true
            );

            if (null === self::$manifestJSON) {
                wp_die(
                    __(
                        'The manifest.json file is not valid JSON file format',
                        'quick'
                    )
                );
            }
        }

        return [];
    }
}
