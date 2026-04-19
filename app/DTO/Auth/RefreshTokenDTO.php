<?php

declare(strict_types=1);

namespace App\DTO\Auth;

final readonly class RefreshTokenDTO
{
    public function __construct(
        public string $refreshToken,
    ) {
    }
}
