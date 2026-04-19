<?php

declare(strict_types=1);

namespace App\DTO\Auth;

final readonly class AuthUserDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {
    }
}
