<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService 
{
    public function __construct(protected User $user)
    {
        
    }

    public function login($request) 
    {
        $user = $this->user->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['message' => 'Invalid Credentials']);
        }

         $oneDay = 60 * 24;

        $token = $user->createToken('token', ['*'], now()->addWeek())->plainTextToken;
        $cookie = cookie('jwt', $token, $oneDay);
        
        return [
            'token' => $token,
            'cookie' => $cookie,
            'name' => ucwords($user->name)
        ];
    }
}