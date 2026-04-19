<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\DTO\Auth\AccessTokenDTO;
use App\DTO\Auth\AuthResultDTO;
use App\DTO\Auth\AuthUserDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\LogoutDTO;
use App\DTO\Auth\RefreshTokenDTO;
use App\Repositories\Auth\AuthTokenIssuerInterface;
use App\Services\Auth\AuthService;
use PHPUnit\Framework\TestCase;

final class AuthServiceTest extends TestCase
{
    public function test_login_delegates_to_token_issuer(): void
    {
        $issuer = new class implements AuthTokenIssuerInterface {
            public bool $called = false;

            public function login(LoginDTO $credentials): AuthResultDTO
            {
                $this->called = true;

                return new AuthResultDTO(
                    accessToken: 'access-token',
                    tokenType: 'Bearer',
                    refreshToken: null,
                    expiresIn: null,
                );
            }

            public function refresh(RefreshTokenDTO $refreshToken): AuthResultDTO
            {
                self::fail('Refresh should not be called.');
            }

            public function logout(LogoutDTO $logout): void
            {
                self::fail('Logout should not be called.');
            }

            public function me(AccessTokenDTO $accessToken): AuthUserDTO
            {
                self::fail('Me should not be called.');
            }
        };

        $service = new AuthService($issuer);

        $result = $service->login(new LoginDTO(
            email: 'john@example.com',
            password: 'secret',
            remember: false,
        ));

        self::assertTrue($issuer->called);
        self::assertSame('access-token', $result->accessToken);
        self::assertSame('Bearer', $result->tokenType);
    }

    public function test_refresh_delegates_to_token_issuer(): void
    {
        $issuer = new class implements AuthTokenIssuerInterface {
            public bool $called = false;

            public function login(LoginDTO $credentials): AuthResultDTO
            {
                self::fail('Login should not be called.');
            }

            public function refresh(RefreshTokenDTO $refreshToken): AuthResultDTO
            {
                $this->called = true;

                return new AuthResultDTO(
                    accessToken: 'new-access-token',
                    tokenType: 'Bearer',
                    refreshToken: 'new-refresh-token',
                    expiresIn: 3600,
                );
            }

            public function logout(LogoutDTO $logout): void
            {
                self::fail('Logout should not be called.');
            }

            public function me(AccessTokenDTO $accessToken): AuthUserDTO
            {
                self::fail('Me should not be called.');
            }
        };

        $service = new AuthService($issuer);

        $result = $service->refresh(new RefreshTokenDTO('refresh-token'));

        self::assertTrue($issuer->called);
        self::assertSame('new-access-token', $result->accessToken);
        self::assertSame('new-refresh-token', $result->refreshToken);
    }

    public function test_logout_delegates_to_token_issuer(): void
    {
        $issuer = new class implements AuthTokenIssuerInterface {
            public bool $called = false;

            public function login(LoginDTO $credentials): AuthResultDTO
            {
                self::fail('Login should not be called.');
            }

            public function refresh(RefreshTokenDTO $refreshToken): AuthResultDTO
            {
                self::fail('Refresh should not be called.');
            }

            public function logout(LogoutDTO $logout): void
            {
                $this->called = true;
            }

            public function me(AccessTokenDTO $accessToken): AuthUserDTO
            {
                self::fail('Me should not be called.');
            }
        };

        $service = new AuthService($issuer);

        $service->logout(new LogoutDTO('access-token'));

        self::assertTrue($issuer->called);
    }

    public function test_me_delegates_to_token_issuer(): void
    {
        $issuer = new class implements AuthTokenIssuerInterface {
            public bool $called = false;

            public function login(LoginDTO $credentials): AuthResultDTO
            {
                self::fail('Login should not be called.');
            }

            public function refresh(RefreshTokenDTO $refreshToken): AuthResultDTO
            {
                self::fail('Refresh should not be called.');
            }

            public function logout(LogoutDTO $logout): void
            {
                self::fail('Logout should not be called.');
            }

            public function me(AccessTokenDTO $accessToken): AuthUserDTO
            {
                $this->called = true;

                return new AuthUserDTO(
                    id: 1,
                    name: 'John Doe',
                    email: 'john@example.com',
                );
            }
        };

        $service = new AuthService($issuer);

        $user = $service->me(new AccessTokenDTO('access-token'));

        self::assertTrue($issuer->called);
        self::assertSame(1, $user->id);
        self::assertSame('John Doe', $user->name);
        self::assertSame('john@example.com', $user->email);
    }
}
