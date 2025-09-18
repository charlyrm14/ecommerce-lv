<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Resources\User\LoginUserResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{
    Auth,
    Log
};

class AuthController extends Controller
{
    /**
     * Handle a login request and issue a Bearer token if the credentials are valid.
     *
     * This method validates the user's email and password. If authentication succeeds,
     * it returns the authenticated user's data along with a Passport-issued access token.
     * Otherwise, it responds with an invalid credentials error.
     *
     * @param \App\Http\Requests\UserLoginRequest $request The login request containing email and password.
     * @return \Illuminate\Http\JsonResponse JSON response with user data and access token, or an error message.
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        try {
            
            if(!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();
            $user->load('role');
            $token = $user->createToken('auth-token');

            return response()->json([
                'data' => [
                    'user' => new LoginUserResource($user),
                    'token' => $token->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($token->token->expires_at)->toDateTimeString()
                ]
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Login error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Handle a user logout an revoke token
     *
     * @return \Illuminate\Http\JsonResponse JSON response with user data and access token, or an error message.
     *
     */
    public function logout(): JsonResponse
    {
        try {

            $user = Auth::user();

            if ($user && $user->token()) {
                $user->token()->revoke();
            }
            
            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);

        } catch (\Throwable $e) {

            Log::error("Logout error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
