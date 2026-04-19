<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\DTO\Auth\AccessTokenDTO;
use App\DTO\Auth\AuthResultDTO;
use App\DTO\Auth\AuthUserDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\LogoutDTO;
use App\DTO\Auth\RefreshTokenDTO;

interface AuthTokenIssuerInterface
{
    public function login(LoginDTO $credentials): AuthResultDTO;

    public function refresh(RefreshTokenDTO $refresh): AuthResultDTO;

    public function logout(LogoutDTO $logout): void;

    public function me(AccessTokenDTO $access): AuthUserDTO;
}
