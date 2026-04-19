<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers;

use App\DTO\Auth\AccessTokenDTO;
use App\DTO\Auth\AuthResultDTO;
use App\DTO\Auth\AuthUserDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\LogoutDTO;
use App\DTO\Auth\RefreshTokenDTO;
use App\Http\Controllers\AuthController;
use App\Repositories\Auth\AuthTokenIssuerInterface;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Tests\TestCase;

final class AuthControllerTest extends TestCase
{
    public function test_login_returns_access_token_response(): void
    {
        $issuer = $this->createMock(AuthTokenIssuerInterface::class);

        $issuer
            ->expects(self::once())
            ->method('login')
            ->with(self::callback(
                fn (LoginDTO $credentials): bool => $credentials->email === 'user@example.com'
                    && $credentials->password === 'secret'
                    && $credentials->remember === false
            ))
            ->willReturn(new AuthResultDTO(
                accessToken: 'plain-text-token',
                tokenType: 'Bearer',
                refreshToken: null,
                expiresIn: null,
            ));

        $controller = $this->controllerWithIssuer($issuer);

        $response = $controller->login(Request::create('/api/auth/login', 'POST', [
            'email' => 'user@example.com',
            'password' => 'secret',
            'remember' => false,
        ]));

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([
            'token_type' => 'Bearer',
            'access_token' => 'plain-text-token',
            'expires_in' => null,
        ], $response->getData(true));
    }

    public function test_login_returns_unauthorized_for_invalid_credentials(): void
    {
        $issuer = $this->createMock(AuthTokenIssuerInterface::class);

        $issuer
            ->expects(self::once())
            ->method('login')
            ->willThrowException(new InvalidArgumentException('Invalid credentials.'));

        $controller = $this->controllerWithIssuer($issuer);

        $response = $controller->login(Request::create('/api/auth/login', 'POST', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]));

        self::assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertSame([
            'message' => 'Invalid credentials.',
        ], $response->getData(true));
    }

    public function test_logout_returns_unauthorized_without_bearer_token(): void
    {
        $issuer = $this->createMock(AuthTokenIssuerInterface::class);

        $issuer
            ->expects(self::never())
            ->method('logout');

        $controller = $this->controllerWithIssuer($issuer);

        $response = $controller->logout(Request::create('/api/auth/logout', 'POST'));

        self::assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertSame([
            'message' => 'Unauthenticated.',
        ], $response->getData(true));
    }

    public function test_logout_returns_unauthorized_for_invalid_token(): void
    {
        $issuer = $this->createMock(AuthTokenIssuerInterface::class);

        $issuer
            ->expects(self::once())
            ->method('logout')
            ->with(self::callback(
                fn (LogoutDTO $logout): bool => $logout->accessToken === 'invalid-token'
            ))
            ->willThrowException(new InvalidArgumentException('Invalid access token.'));

        $controller = $this->controllerWithIssuer($issuer);

        $response = $controller->logout(
            $this->requestWithBearerToken('/api/auth/logout', 'POST', 'invalid-token')
        );

        self::assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertSame([
            'message' => 'Invalid access token.',
        ], $response->getData(true));
    }

    public function test_logout_returns_no_content(): void
    {
        $issuer = new class implements AuthTokenIssuerInterface {
            public ?string $receivedToken = null;

            public function login(LoginDTO $credentials): AuthResultDTO
            {
                throw new \BadMethodCallException('Login should not be called.');
            }

            public function refresh(RefreshTokenDTO $refreshToken): AuthResultDTO
            {
                throw new \BadMethodCallException('Refresh should not be called.');
            }

            public function logout(LogoutDTO $logout): void
            {
                $this->receivedToken = $logout->accessToken;
            }

            public function me(AccessTokenDTO $accessToken): AuthUserDTO
            {
                throw new \BadMethodCallException('Me should not be called.');
            }
        };

        $controller = $this->controllerWithIssuer($issuer);

        $response = $controller->logout(
            $this->requestWithBearerToken('/api/auth/logout', 'POST', 'valid-token')
        );

        self::assertSame(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertSame('valid-token', $issuer->receivedToken);
    }

    public function test_me_returns_unauthorized_without_bearer_token(): void
    {
        $issuer = $this->createMock(AuthTokenIssuerInterface::class);

        $issuer
            ->expects(self::never())
            ->method('me');

        $controller = $this->controllerWithIssuer($issuer);

        $response = $controller->me(Request::create('/api/auth/me', 'GET'));

        self::assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertSame([
            'message' => 'Unauthenticated.',
        ], $response->getData(true));
    }

    public function test_me_returns_unauthorized_for_invalid_token(): void
    {
        $issuer = $this->createMock(AuthTokenIssuerInterface::class);

        $issuer
            ->expects(self::once())
            ->method('me')
            ->with(self::callback(
                fn (AccessTokenDTO $accessToken): bool => $accessToken->accessToken === 'invalid-token'
            ))
            ->willThrowException(new InvalidArgumentException('Invalid access token.'));

        $controller = $this->controllerWithIssuer($issuer);

        $response = $controller->me(
            $this->requestWithBearerToken('/api/auth/me', 'GET', 'invalid-token')
        );

        self::assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertSame([
            'message' => 'Invalid access token.',
        ], $response->getData(true));
    }

    public function test_me_returns_authenticated_user(): void
    {
        $issuer = new class implements AuthTokenIssuerInterface {
            public ?string $receivedToken = null;

            public function login(LoginDTO $credentials): AuthResultDTO
            {
                throw new \BadMethodCallException('Login should not be called.');
            }

            public function refresh(RefreshTokenDTO $refreshToken): AuthResultDTO
            {
                throw new \BadMethodCallException('Refresh should not be called.');
            }

            public function logout(LogoutDTO $logout): void
            {
                throw new \BadMethodCallException('Logout should not be called.');
            }

            public function me(AccessTokenDTO $accessToken): AuthUserDTO
            {
                $this->receivedToken = $accessToken->accessToken;

                return new AuthUserDTO(
                    id: 1,
                    name: 'John Doe',
                    email: 'john@example.com',
                );
            }
        };

        $controller = $this->controllerWithIssuer($issuer);

        $response = $controller->me(
            $this->requestWithBearerToken('/api/auth/me', 'GET', 'valid-token')
        );

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame('valid-token', $issuer->receivedToken);
        self::assertSame([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ], $response->getData(true));
    }

    private function controllerWithIssuer(AuthTokenIssuerInterface $issuer): AuthController
    {
        return new AuthController(new AuthService($issuer));
    }

    private function requestWithBearerToken(string $uri, string $method, string $token): Request
    {
        return Request::create($uri, $method, [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
        ]);
    }
}
