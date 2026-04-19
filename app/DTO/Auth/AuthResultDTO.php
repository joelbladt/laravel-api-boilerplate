<?php

declare(strict_types=1);

namespace App\DTO\Auth;

final readonly class AuthResultDTO
{
    public function __construct(
        public string $accessToken,
        public string $tokenType = 'Bearer',
        public ?string $refreshToken = null,
        public ?int $expiresIn = null,
    ) {
    }
}
