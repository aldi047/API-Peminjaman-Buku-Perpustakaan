<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $authBusinessLayer;

    public function __construct()
    {
        $this->authBusinessLayer = new \App\BusinessLayer\AuthBusinessLayer();
    }

    public function register(Request $request)
    {
        $data = $this->authBusinessLayer->register($request);
        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {
        $data = $this->authBusinessLayer->login($request);
        return response()->json($data, $data['code']);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        $data = $this->authBusinessLayer->logout();
        return response()->json($data, $data['code']);
    }

    public function refresh()
    {
        $data = $this->authBusinessLayer->refresh();
        return response()->json($data, $data['code']);
    }
}
