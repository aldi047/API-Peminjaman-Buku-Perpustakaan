<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userBusinessLayer;

    public function __construct()
    {
        $this->userBusinessLayer = new \App\BusinessLayer\UserBusinessLayer();
    }

    public function getAllUsers(Request $request)
    {
        $data = $this->userBusinessLayer->getAllUsers($request);
        return response()->json($data, $data['code']);
    }

    public function editUser()
    {
        $data = $this->userBusinessLayer->editUser(request());
        return response()->json($data, $data['code']);
    }

    public function deleteUser($user_id)
    {
        $data = $this->userBusinessLayer->deleteUser($user_id);
        return response()->json($data, $data['code']);
    }
}
