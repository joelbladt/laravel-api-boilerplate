<?php

declare(strict_types=1);

namespace App\DTO\Auth;

final readonly class LogoutDTO
{
    public function __construct(
        public string $accessToken,
    ) {
    }
}
