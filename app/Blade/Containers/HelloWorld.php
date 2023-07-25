<?php

declare(strict_types=1);

namespace Quick\Blade\Containers;

use Quick\Interfaces\{
    Renderer,
    RendererArguments
};
use Quick\Blade\Engine;

use function Quick\WP\wrapFormHandler;

class HelloWorld implements Renderer, RendererArguments {
    public static function getArguments(array $params = []): array {
        return wrapFormHandler('helloWorld', [
            'HelloWorldVersion' => 'What is the version of this plugin?',
        ]);
    }

    public static function render(array $params = []): void {
        if (!empty($params['includeHeader'])) {
            get_header(
                is_string($params['includeHeader']) ? $params['includeHeader'] : null
            );
        }

        try {
            echo Engine::getInstance()
                ->getEngine()
                ->run('HelloWorld.Example', array_merge($params, self::getArguments($params)));
        } catch (\Exception  $e) {
            wp_die(
                $e->getMessage(),
                'Quick App Error',
                ['response' => 500, 'back_link' => true]
            );
        }

        if (!empty($params['includeFooter'])) {
            get_footer(
                is_string($params['includeFooter']) ? $params['includeFooter'] : null
            );
        }
    }
}
