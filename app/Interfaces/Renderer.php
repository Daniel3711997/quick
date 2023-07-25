<?php

declare(strict_types=1);

namespace Quick\Interfaces;

interface Renderer {
    public static function render(array $params = []): void;
}
