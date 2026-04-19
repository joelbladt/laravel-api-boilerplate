<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Auth\AccessTokenDTO;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\LogoutDTO;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Login and receive Sanctum access token",
     *     security={},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret"),
     *             @OA\Property(property="remember", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="expires_in", type="integer", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        try {
            $result = $this->authService->login(
                new LoginDTO(
                    email: $validated['email'],
                    password: $validated['password'],
                    remember: (bool) ($validated['remember'] ?? false),
                )
            );
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'token_type' => $result->tokenType,
            'access_token' => $result->accessToken,
            'expires_in' => $result->expiresIn,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout and invalidate current Sanctum token",
     *     @OA\Response(response=204, description="Logged out"),
     *     @OA\Response(response=401, description="Unauthenticated or invalid token")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $this->authService->logout(new LogoutDTO($token));
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"Auth"},
     *     summary="Get authenticated user from current Sanctum token",
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated or invalid token")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $user = $this->authService->me(new AccessTokenDTO($token));
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}

