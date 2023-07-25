<?php

declare(strict_types=1);

namespace Quick\API\Versions\V1\Endpoints;

use WP_REST_Server;
use WP_REST_Response;
use Quick\API\Versions\V1\API;
use Quick\Interfaces\API as APIInterface;

class Profile extends API implements APIInterface {
    public string $route = 'profile';

    public function options(): array {
        return [
            'callback' => [$this, 'getProfile'],
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => '__return_true',
        ];
    }

    public function getProfile(): WP_REST_Response {
        return $this->createResponse([
            'id' => 1234,
            'name' => 'John Doe',
        ]);
    }
}
