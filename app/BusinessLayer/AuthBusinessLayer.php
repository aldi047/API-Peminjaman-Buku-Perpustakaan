<?php

namespace App\BusinessLayer;

use App\Models\User;
use App\PresentationLayer\ResponseCreatorPresentationLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthBusinessLayer
{
    public function register(Request $userData)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make($userData->all(), [
                'nama'      => 'required|string',
                'email'     => 'required|email|unique:users',
                'role_id'   => 'required',
                'password'  => 'required',
            ], [
                'nama.required'     => 'Nama harus diisi',
                'email.required'    => 'Email harus diisi',
                'email.email'       => 'Pastikan format email benar',
                'email.unique'      => 'Email sudah dipakai',
                'role_id.required'  => 'Role pengguna harus diisi',
                'password.required' => 'Password harus diisi'
            ]);

            if ($validation->fails()) {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    401, 'Gagal Validasi Data Pengguna',
                    null, $validation->errors());
                return $response->getResponse();
            }

            $data = new User();
            $data->nama = $userData->nama;
            $data->email = $userData->email;
            $data->role_id = $userData->role_id;
            $data->password = app('hash')->make($userData->password);
            $data->save();
            DB::commit();

            $response = new ResponseCreatorPresentationLayer(
                201, 'Berhasil Menambahkan Pengguna', [], null);
        } catch (\Exception $e) {
            DB::rollBack();
            $response = new ResponseCreatorPresentationLayer(
                500, 'Terjadi Kesalahan Pada Server : ' . $e->getMessage(), null, $e);
        }
        return $response->getResponse();
    }

    public function login(Request $userData)
    {
        try {
            $credentials = request(['email', 'password']);

            if (! $token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $data_login = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ];

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Login', $data_login, []);
        } catch (\Exception $e) {
            DB::rollBack();
            $response = new ResponseCreatorPresentationLayer(
                500, 'Terjadi Kesalahan Pada Server : ' . $e->getMessage(), null, $e);
        }
        return $response->getResponse();
    }

    public function logout()
    {
        try {
            auth()->logout();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Logout', [], null);
        } catch (\Exception $e) {
            DB::rollBack();
            $response = new ResponseCreatorPresentationLayer(
                500, 'Terjadi Kesalahan Pada Server : ' . $e->getMessage(), null, $e);
        }
        return $response->getResponse();
    }

    public function refresh()
    {
        try {
            $data_login = [
                'access_token' => Auth::refresh(),
                'token_type' => 'Bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ];

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Mengambil Refresh Token', $data_login, []);
        } catch (\Exception $e) {
            DB::rollBack();
            $response = new ResponseCreatorPresentationLayer(
                500, 'Terjadi Kesalahan Pada Server : ' . $e->getMessage(), null, $e);
        }
        return $response->getResponse();
    }
}