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
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Laravel\Sanctum\PersonalAccessToken;
use LogicException;

final class SanctumTokenIssuer implements AuthTokenIssuerInterface
{
    public function login(LoginDTO $credentials): AuthResultDTO
    {
        $user = User::query()
            ->where('email', $credentials->email)
            ->first();

        if (!$user || !Hash::check($credentials->password, $user->password)) {
            throw new InvalidArgumentException('Invalid credentials.');
        }

        $tokenName = $credentials->remember ? 'auth-token-remember' : 'auth-token';
        $plainTextToken = $user->createToken($tokenName)->plainTextToken;

        return new AuthResultDTO(
            accessToken: $plainTextToken,
            tokenType: 'Bearer',
            refreshToken: null,
            expiresIn: null,
        );
    }

    public function refresh(RefreshTokenDTO $refresh): AuthResultDTO
    {
        $access = PersonalAccessToken::query();
        throw new LogicException('Sanctum does not support refresh tokens by default. Please login again.');
    }

    public function logout(LogoutDTO $logout): void
    {
        $token = PersonalAccessToken::findToken($logout->accessToken);

        if (!$token) {
            throw new InvalidArgumentException('Invalid access token.');
        }

        $token->delete();
    }

    public function me(AccessTokenDTO $access): AuthUserDTO
    {
        $token = PersonalAccessToken::findToken($access->accessToken);

        if (!$token) {
            throw new InvalidArgumentException('Invalid access token.');
        }

        $user = $token->tokenable;

        if (!$user instanceof User) {
            throw new InvalidArgumentException('Token is not associated with a user.');
        }

        return new AuthUserDTO(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}
