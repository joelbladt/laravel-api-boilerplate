<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTO\Auth\AccessTokenDTO;
use App\DTO\Auth\AuthResultDTO;
use App\DTO\Auth\AuthUserDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\LogoutDTO;
use App\DTO\Auth\RefreshTokenDTO;
use App\Repositories\Auth\AuthTokenIssuerInterface;


final readonly class AuthService
{
    public function __construct(
        private AuthTokenIssuerInterface $tokenIssuer,
    ) {
    }

    public function login(LoginDTO $credentials): AuthResultDTO
    {
        return $this->tokenIssuer->login($credentials);
    }

    public function refresh(RefreshTokenDTO $refreshToken): AuthResultDTO
    {
        return $this->tokenIssuer->refresh($refreshToken);
    }

    public function logout(LogoutDTO $logout): void
    {
        $this->tokenIssuer->logout($logout);
    }

    public function me(AccessTokenDTO $accessToken): AuthUserDTO
    {
        return $this->tokenIssuer->me($accessToken);
    }
}
