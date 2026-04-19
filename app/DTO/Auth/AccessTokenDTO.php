<?php

declare(strict_types=1);

namespace App\DTO\Auth;

final readonly class AccessTokenDTO
{
    public function __construct(
        public string $accessToken,
    ) {
    }
}
