<?php

declare(strict_types=1);

namespace Quick\Server;

use Quick\Server\Methods\UserProfileData;

class ServerClient {
    public static function getUserProfileData(): array {
        return UserProfileData::run();
    }
}
