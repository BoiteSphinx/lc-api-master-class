<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! Auth::attempt(credentials: ['email' => $validated['email'], 'password' => $validated['password']])) {
            return $this->error(message: 'Invalid credentials', statusCode: 401);
        }

        $user = User::query()->firstWhere('email', $validated['email']);

        return $this->ok(
            message: 'Authenticated',
            data: [
                'token' => $user->createToken('API token for ' . $user->email)->plainTextToken
            ]
        );
    }
}
