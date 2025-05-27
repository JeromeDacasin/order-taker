<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $service)
    {
        
    }

    public function login(Request $request) 
    {
        return $this->service->login($request);
    }
}
