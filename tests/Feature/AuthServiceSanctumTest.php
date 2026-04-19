<?php

namespace Tests\Feature;

use App\DTO\Auth\AccessTokenDTO;
use App\DTO\Auth\LogoutDTO;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthServiceSanctumTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_and_logout_flow_with_sanctum_token(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('feature-test')->plainTextToken;

        $this->assertNotNull(PersonalAccessToken::findToken($token));

        $auth = app(AuthService::class);

        $me = $auth->me(new AccessTokenDTO($token));

        $this->assertSame($user->id, $me->id);
        $this->assertSame($user->name, $me->name);
        $this->assertSame($user->email, $me->email);

        $auth->logout(new LogoutDTO($token));

        $this->assertNull(PersonalAccessToken::findToken($token));
    }
}
