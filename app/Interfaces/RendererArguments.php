<?php

declare(strict_types=1);

namespace Quick\Interfaces;

interface RendererArguments {
    public static function getArguments(array $params = []): array;
}
