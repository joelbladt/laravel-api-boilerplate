<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\Auth\AccessTokenDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\LogoutDTO;
use App\DTO\Auth\RefreshTokenDTO;
use App\Models\Publisher;
use App\Models\User;
use App\Services\Auth\SanctumTokenIssuer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Sanctum\PersonalAccessToken;
use LogicException;
use Tests\TestCase;

final class SanctumTokenIssuerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_sanctum_access_token(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret'),
        ]);

        $issuer = new SanctumTokenIssuer();

        $result = $issuer->login(new LoginDTO(
            email: 'john@example.com',
            password: 'secret',
            remember: false,
        ));

        self::assertSame('Bearer', $result->tokenType);
        self::assertNotEmpty($result->accessToken);
        self::assertNull($result->refreshToken);
        self::assertNull($result->expiresIn);
    }

    public function test_login_throws_exception_for_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret'),
        ]);

        $issuer = new SanctumTokenIssuer();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid credentials.');

        $issuer->login(new LoginDTO(
            email: 'john@example.com',
            password: 'wrong-password',
            remember: false,
        ));
    }

    public function test_refresh_throws_logic_exception(): void
    {
        $issuer = new SanctumTokenIssuer();

        $this->expectException(LogicException::class);

        $issuer->refresh(new RefreshTokenDTO('refresh-token'));
    }

    public function test_me_returns_authenticated_user_from_access_token(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('secret'),
        ]);

        $plainTextToken = $user->createToken('auth-token')->plainTextToken;

        $issuer = new SanctumTokenIssuer();

        $authUser = $issuer->me(new AccessTokenDTO($plainTextToken));

        self::assertSame($user->id, $authUser->id);
        self::assertSame('John Doe', $authUser->name);
        self::assertSame('john@example.com', $authUser->email);
    }

    public function test_me_throws_exception_for_invalid_access_token(): void
    {
        $issuer = new SanctumTokenIssuer();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid access token.');

        $issuer->me(new AccessTokenDTO('invalid-token'));
    }

    public function test_logout_deletes_access_token(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
        ]);

        $plainTextToken = $user->createToken('auth-token')->plainTextToken;

        $issuer = new SanctumTokenIssuer();

        $issuer->logout(new LogoutDTO($plainTextToken));

        self::assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_throws_exception_for_invalid_access_token(): void
    {
        $issuer = new SanctumTokenIssuer();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid access token.');

        $issuer->logout(new LogoutDTO('invalid-token'));
    }

    public function test_me_throws_exception_when_token_is_not_associated_with_user(): void
    {
        $publisher = Publisher::factory()->create();

        $plainTextToken = Str::random(40);

        $token = new PersonalAccessToken([
            'name' => 'auth-token',
            'token' => hash('sha256', $plainTextToken),
            'abilities' => ['*'],
        ]);

        $token->tokenable()->associate($publisher);
        $token->save();

        $issuer = new SanctumTokenIssuer();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Token is not associated with a user.');

        $issuer->me(new AccessTokenDTO(
            accessToken: "{$token->id}|{$plainTextToken}",
        ));
    }
}
