<?php

/**
 * Quick
 *
 * @license           MIT
 * @package           Quick
 * @author            Daniel3711997
 * @copyright         2023 © Next Level Digital
 *
 * @wordpress-plugin
 * Plugin Name:       Quick
 * Plugin URI:        https://gotonxtlevel.com/quick
 * Description:       Integrates React JS with WordPress
 * Version:           1.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Daniel3711997
 * Author URI:        https://github.com/Daniel3711997
 * Text Domain:       quick
 * Domain Path:       /languages
 * License:           MIT
 * Update URI:        https://gotonxtlevel.com/quick/update
 * License URI:       https://github.com/Daniel3711997/quick/blob/main/LICENSE.md
 */

declare(strict_types=1);

namespace Quick\Plugin;

use Dotenv\Dotenv;
use Quick\Database\CLI;
use Quick\Framework\Quick as App;
use Quick\Cache\Engine as CacheEngine;

if (!defined('ABSPATH')) {
    exit;
}

if (defined('WP_CLI')) {
    \WP_CLI::add_command('quick', CLI::class);
}

function loadPackage() {
    if (!file_exists(__DIR__ . '/package.json')) {
        wp_die(
            __(
                'The package.json file is missing',
                'quick'
            )
        );
    }

    return json_decode(file_get_contents(__DIR__ . '/package.json'), true);
}

$package = loadPackage();

define('QUICK_PLUGIN_HOME_URI', home_url());
define('QUICK_SYSTEM', 'doctrine-annotations');
define('QUICK_PLUGIN_URI', plugin_dir_url(__FILE__));
define('QUICK_PLUGIN_DIRECTORY', plugin_dir_path(__FILE__));
define('QUICK_PUBLIC', $package['quickApplicationPublicPath']);
define('QUICK_ENVIRONMENT', $package['quickApplicationNodeEnvironment']);

function getQuickPublic(): string {
    return QUICK_PUBLIC;
}

function getSiteHomeURI(): string {
    return QUICK_PLUGIN_HOME_URI;
}


function getQuickPluginURI(): string {
    return QUICK_PLUGIN_URI;
}

function getQuickEnvironment(): string {
    return QUICK_ENVIRONMENT;
}

function getQuickPluginDirectory(): string {
    return QUICK_PLUGIN_DIRECTORY;
}

function getQuickAssetsURI(): string {
    return getQuickPluginURI() . 'assets';
}

function getQuickAssetsDirectory(): string {
    return getQuickPluginDirectory() . 'assets';
}

function isProduction(): bool {
    return getQuickEnvironment() === 'production';
}

function isDevelopment(): bool {
    return getQuickEnvironment() === 'development';
}

if (!file_exists(__DIR__ . '/vendor')) {
    wp_die(
        __(
            'No vendor folder found',
            'quick'
        )
    );
}

if (isProduction() && !file_exists(__DIR__ . '/build')) {
    wp_die(
        __(
            'No build folder found',
            'quick'
        )
    );
}

register_theme_directory(getQuickPluginDirectory() . 'themes');

add_filter('theme_root_uri', function (string $themeRootURI): string {
    if (false === strpos($themeRootURI, 'http') && false !== strpos($themeRootURI, '/themes')) {
        $themeRootURI = getQuickPluginURI() . 'themes';
    }

    return $themeRootURI;
});

set_exception_handler(
    function (\Throwable $exception) {
        $error = sprintf(
            '(%d) There was an error in the file %s on line %d: %s',
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getMessage()
        );

        error_log(
            $error
        );

        wp_die(
            $error
        );
    }
);

set_error_handler(
    function (int $errorNumber, string $errorString, string $errorFile, int $errorLine) {
        if (!(error_reporting() & $errorNumber)) {
            return false;
        }

        $error = sprintf(
            '(%d) There was an error in the file %s on line %d: %s',
            $errorNumber,
            $errorFile,
            $errorLine,
            htmlspecialchars(
                $errorString
            )
        );

        error_log(
            $error
        );

        if (E_USER_ERROR === $errorNumber) {
            wp_die(
                $error
            );
        } else {
            echo $error;
        }

        return true;
    }
);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/Framework/Initialize.php';

$dotenv = Dotenv::createImmutable(__DIR__);

if (file_exists(__DIR__ . '/.env')) {
    $dotenv->load();
}

function readDirectory(string $directory): array {
    $cache = CacheEngine::getInstance();
    $cacheKey = md5('readDirectory' . '-' . $directory);

    if (isProduction() && $cache->has($cacheKey)) {
        return $cache->get($cacheKey);
    }

    if (!is_dir($directory)) {
        return [];
    }

    $files = array_diff(scandir($directory) ?: [], ['.', '..', '.DS_Store']);

    if (isProduction()) {
        $cache->set($cacheKey, $files, 0);
    }

    return $files;
}

add_shortcode(
    'quick-framework',
    function ($reactJSShortCodeAttributes) {
        $attributes = shortcode_atts([
            'id' => 'root',
            'echo' => false,
            'loader' => '<div class="quick-framework-loader"><p>Loading...</p></div>',
        ], $reactJSShortCodeAttributes);

        $noScript = '<noscript>Could not load the application the JavaScript runtime seems to be disabled</noscript>';

        /**
         * Do not escape the loader because HTML is allowed to be passed in the shortcode
         */
        $html = '<div id="' . esc_attr($attributes['id']) . '">' . $attributes['loader'] . $noScript . '</div>';

        if ($attributes['echo']) {
            echo $html;
        } else {
            return $html;
        }

        return null;
    }
);

add_action('init', [App::class, 'registerRules']);
add_action('wp_enqueue_scripts', [App::class, 'load']);
add_action('admin_enqueue_scripts', [App::class, 'load']);
add_filter('query_vars', [App::class, 'registerQueryVars']);

add_filter(
    'script_loader_tag',
    function (string $tag, string $handle): string {

        if (isDevelopment()) {
            if ('vite-client' === $handle) {
                return str_replace(' src', ' type="module" crossorigin src', $tag);
            }

            if (false !== strpos($tag, '__vite_plugin_react_preamble_installed__')) {
                return str_replace(' id', ' type="module" id', $tag);
            }
        }

        if (false !== strpos($tag, 'quick-application')) {
            return str_replace(' src', ' type="module" crossorigin src', $tag);
        }

        return $tag;
    },
    10,
    2
);
