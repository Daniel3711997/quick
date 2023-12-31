<?php

declare(strict_types=1);

namespace Quick\API\Versions\V1;

use Quick\API\Response;

class API extends Response {
    public string $route;

    public function options(): array {
        return [];
    }

    public string $version = 'v1';
    public string $namespace = 'quick';

    public function register(): void {
        if (empty($this->route)) {
            return;
        }

        $options = $this->options();

        if (empty($options)) {
            return;
        }

        register_rest_route("$this->namespace/$this->version", "/$this->route", $options);
    }
}
