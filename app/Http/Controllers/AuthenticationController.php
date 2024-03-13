<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticationRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticationController extends Controller
{
    public function login(AuthenticationRequest $request): Response
    {
        $data = $request->validated();

        if (!Auth::attempt($data)) {
            return response([
                'message' => 'Invalid credentials'
            ], 400);
        }

        $user = User::where('email', '=', $data['email'])->first();

        Auth::login($user);


        return response([
            'token' => $user->createToken('login')->plainTextToken,
        ], 200);
    }

    public function logout(): Response
    {
        Auth::user()->tokens()->each(fn (PersonalAccessToken $token) => $token->delete());

        return response(
            ['message' => 'Successfully logged out'],
            200
        );
    }
}
