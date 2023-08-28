<?php

namespace App\Http\Controllers\Api\Auth;

use Hash;
use Auth;
use App\Models\User;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response('Incorrect data', Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('api');

        return response(['token' => $token->plainTextToken]);
    }

    public function logout()
    {
        $user = Auth::user();

        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response(['message' => 'Logged out successfully']);
    }
}
