<?php

declare(strict_types=1);

namespace Quick\Server\Methods;

class UserProfileData {
    public static function run(): array {
        return [
            "success" => true,
            "data" => [
                "id" => 1234,
            ]
        ];
    }
}
