<?php

declare(strict_types=1);

namespace Quick\Framework;

use Quick\Server\ServerClient;

use function Quick\Plugin\{
    isProduction,
    isDevelopment,
    getQuickPluginURI,
};

class Quick {
    private static array $server = [];

    private static string $routesPath = QUICK_PLUGIN_DIRECTORY . 'src/routes.json';
    private static string $routesHashPath = QUICK_PLUGIN_DIRECTORY . 'src/routes.hash';
    private static string $localManifestPath = QUICK_PLUGIN_DIRECTORY . 'build/manifest.json';

    public static function load(): void {
        global $preloadJS, $preloadCSS, $preloadFonts;

        $preloadJS = [];
        $preloadCSS = [];
        $preloadFonts = [];
        $localizedId = null;
        $alreadyLocalized = false;
        $routes = self::getRoutes();
        $alreadyUsedViteClient = false;
        $alreadyUsedReactRefresh = false;
        $alreadyInjectedReactBlock = false;
        $manifest = isDevelopment() ? [] : self::getManifest();

        foreach ($routes as $route) {
            $includeFramework = false;

            if (is_admin() && empty($route['admin'])) {
                continue;
            }

            if (isset($route['tests']) && is_array($route['tests'])) {
                $operator = $route['operator'] ?? 'AND';

                foreach ($route['tests'] as $test) {
                    if (function_exists($test['function'])) {
                        $result = call_user_func_array(
                            $test['function'],
                            $test['arguments'] ?? []
                        );

                        if ('AND' === $operator) {
                            $includeFramework = true === $result;

                            if (false === $includeFramework) {
                                break;
                            }
                        }

                        if ('OR' === $operator && true === $result) {
                            $includeFramework = true;
                            break;
                        }
                    }
                }
            }

            if ($includeFramework) {
                $foundRoute = $manifest[$route['entry']['path']] ?? [];

                if (isProduction() && isset($route['fonts']['used']['woff2'])) {
                    $preloadFonts = array_merge($preloadFonts, $route['fonts']['used']['woff2']);
                }

                $css = $foundRoute['css'] ?? [];
                $imports = $foundRoute['imports'] ?? [];
                $js = $foundRoute['file'] ? [$foundRoute['file']] : [$route['entry']['path']];

                if (count($imports) > 0 && isProduction()) {
                    $preloadJS = array_merge($preloadJS, $imports);
                }

                if (isDevelopment() && !$alreadyUsedViteClient) {
                    $alreadyUsedViteClient = true;
                    wp_enqueue_script('vite-client', "http://localhost:4000/@vite/client", [], null, false);
                }

                if (isProduction()) {
                    foreach ($css as $cssRoute) {
                        $id = md5($cssRoute);

                        if (wp_script_is($id)) {
                            continue;
                        }

                        $cssURI = getQuickPluginURI() . 'build/' . $cssRoute;
                        $preloadCSS[] = $cssURI;

                        wp_enqueue_style($id, $cssURI, $route['cssDependencies'] ?? [], null, $route['cssMedia'] ?? 'all');
                    }
                }

                foreach ($js as $jsRoute) {
                    $encodedId = md5($jsRoute);
                    $id = "quick-application@{$encodedId}";

                    if (wp_script_is($id)) {
                        continue;
                    }

                    $jsURI = getQuickPluginURI() . 'build/' . $jsRoute;

                    if (isProduction()) {
                        $preloadJS[] = $jsURI;
                    }

                    wp_enqueue_script(
                        $id,
                        isDevelopment() ? "http://localhost:4000/{$jsRoute}" : $jsURI,
                        $route['jsDependencies'] ?? [],
                        null,
                        $route['jsInFooter'] ?? false
                    );

                    if (isDevelopment() && !$alreadyUsedReactRefresh && 'react' === $route['app']) {
                        $alreadyUsedReactRefresh = true;

                        $plainJavaScript = '
                            import RefreshRuntime from \'http://localhost:4000/@react-refresh\';
                            RefreshRuntime.injectIntoGlobalHook(window);
                            window.$RefreshReg$ = () => {};
                            window.$RefreshSig$ = () => (type) => type;
                            window.__vite_plugin_react_preamble_installed__ = true;
                        ';

                        wp_add_inline_script($id, $plainJavaScript, 'before');
                    }

                    if (!$alreadyInjectedReactBlock && isProduction() && 'react' === $route['app']) {
                        $plainJavaScript = <<<JS
if ('object' === typeof window.__REACT_DEVTOOLS_GLOBAL_HOOK__) {
    for (const property in window.__REACT_DEVTOOLS_GLOBAL_HOOK__) {
        window.__REACT_DEVTOOLS_GLOBAL_HOOK__[property] =
            'function' === typeof window.__REACT_DEVTOOLS_GLOBAL_HOOK__[property] ? function () {} : [];
    }
}
JS;
                        $alreadyInjectedReactBlock = true;
                        wp_add_inline_script($id, $plainJavaScript, 'before');
                    }

                    if (!$alreadyLocalized) {
                        $localizedId = $id;
                        $alreadyLocalized = true;
                    }
                }

                if (isset($route['server'])) {
                    $server = $route['server'];

                    if (method_exists(ServerClient::class, $server['method'])) {
                        self::$server[$server['key']] = call_user_func([ServerClient::class, $server['method']]);
                    }
                }
            }
        }

        if ($localizedId) {
            wp_localize_script($localizedId, 'quickServer', (object) self::$server);
            wp_localize_script($localizedId, 'quickRuntime', Runtime::getRuntimeConfig());
        }
    }

    public static function registerQueryVars(array $vars): array {
        $routes = self::getRoutes();

        foreach ($routes as $route) {
            global $registeredVars;

            if (is_admin() && empty($route['admin'])) {
                continue;
            }

            $includeFramework = false;

            if (isset($route['tests']) && is_array($route['tests'])) {
                $operator = $route['operator'] ?? 'AND';

                foreach ($route['tests'] as $test) {
                    if (function_exists($test['function'])) {
                        $result = call_user_func_array(
                            $test['function'],
                            $test['arguments'] ?? []
                        );

                        if ('AND' === $operator) {
                            $includeFramework = true === $result;

                            if (false === $includeFramework) {
                                break;
                            }
                        }

                        if ('OR' === $operator && true === $result) {
                            $includeFramework = true;
                            break;
                        }
                    }
                }
            }

            if ($includeFramework && isset($route['rewrites']) && is_array($route['rewrites'])) {
                foreach ($route['rewrites'] as $rewrite) {
                    if (isset($rewrite['queryVars']) && is_array($rewrite['queryVars'])) {
                        foreach ($rewrite['queryVars'] as $queryVar) {
                            $vars[] = $queryVar;
                            $registeredVars[] = $queryVar;
                        }
                    }
                }
            }
        }

        return $vars;
    }

    public static function registerRules(): void {
        $routes = self::getRoutes();

        foreach ($routes as $route) {
            if (isset($route['rewrites']) && is_array($route['rewrites'])) {
                foreach ($route['rewrites'] as $rewrite) {
                    add_rewrite_rule($rewrite['regex'], $rewrite['query'], $rewrite['after']);
                }
            }
        }

        $hash = self::getControllerHash();

        if ($hash['currentHash'] !== $hash['previousHash']) {
            flush_rewrite_rules();
            self::writeControllerHash();
        }
    }

    public static function getManifest(): array {
        if (!file_exists(self::$localManifestPath)) {
            wp_die(
                __(
                    'Manifest file not found',
                    'quick'
                )
            );
        }

        return json_decode(file_get_contents(self::$localManifestPath), true);
    }

    private static function ensureRoutesFileExists() {
        if (!file_exists(self::$routesPath)) {
            wp_die(__('Routes file not found', 'quick'));
        }
    }

    private static function getRoutes(): array {
        self::ensureRoutesFileExists();

        return json_decode(file_get_contents(self::$routesPath), true)['routes'];
    }

    private static function writeControllerHash(): void {
        file_put_contents(self::$routesHashPath, md5_file(self::$routesPath));
    }

    private static function getControllerHash(): array {
        self::ensureRoutesFileExists();

        return [
            'currentHash' => md5_file(self::$routesPath),
            'previousHash' => file_exists(self::$routesHashPath) ? file_get_contents(self::$routesHashPath) : null,
        ];
    }
}
