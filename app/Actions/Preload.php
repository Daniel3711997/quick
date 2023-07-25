<?php

declare(strict_types=1);

namespace Quick\Actions;

use Quick\Annotations\Action;
use Quick\Interfaces\Action as ActionInterface;

use function Quick\Plugin\{
    isProduction,
    readDirectory,
    getQuickPublic,
    getQuickPluginDirectory
};

/**
 * @Action(name="wp_head", method="construct", priority=4, id="appPreload")
 */
class Preload implements ActionInterface {
    public static function inArray(string $search, array $array): bool {
        foreach ($array as $value) {
            return false !== strpos($search, $value);
        }

        return false;
    }

    public static function construct(): void {
        global $preloadJS, $preloadCSS, $preloadFonts;

        if (isProduction()) {
            $publicPath = getQuickPublic() . '/app/assets';
            $fontsDirectory = getQuickPluginDirectory() . 'build/app/assets';

            if ($preloadFonts && file_exists($fontsDirectory)) {
                $fonts = array_map(function ($font) use ($fontsDirectory, $preloadFonts) {
                    $fontInfo = pathinfo($font);

                    if ('woff2' === $fontInfo['extension'] && self::inArray($font, $preloadFonts)) {
                        return $fontsDirectory . '/' . $font;
                    }

                    return null;
                }, readDirectory($fontsDirectory));

                foreach ($fonts as $font) {
                    if ($font && is_file($font)) {
                        echo '<link rel="preload" href="' . str_replace(
                            $fontsDirectory,
                            $publicPath,
                            $font
                        ) . '" as="font" type="font/woff2" crossorigin />' . PHP_EOL;
                    }
                }
            }

            foreach ($preloadCSS as $appStyle) {
                echo '<link rel="preload" href="' . $appStyle . '" as="style" />' . PHP_EOL;
            }

            foreach ($preloadJS as $appScript) {
                echo '<link rel="modulepreload" href="' . $appScript . '" as="script" crossorigin />' . PHP_EOL;
            }
        }
    }
}
