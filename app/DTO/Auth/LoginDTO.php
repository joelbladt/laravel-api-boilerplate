<?php

declare(strict_types=1);

namespace App\DTO\Auth;

final readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
    ) {
    }
}
