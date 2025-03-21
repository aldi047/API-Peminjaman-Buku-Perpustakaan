<?php

namespace App\BusinessLayer;

use App\Models\User;
use App\PresentationLayer\ResponseCreatorPresentationLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserBusinessLayer
{
    public function getAllUsers(Request $request)
    {
        try {
            $perPage = $request->get('perPage');

            $users = User::query();

            $data = $perPage ? $users->paginate($perPage) : $users->get();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Mengambil Data Pengguna', $data, null);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function editUser($request)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make(request()->all(), [
                'role_id'   => 'required',
                'user_id'   => 'required',
            ], [
                'role_id.required'  => 'Role harus diisi',
                'user_id.required'  => 'user_id harus diisi',
            ]);

            if ($validation->fails()) {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    401, 'Gagal Validasi Edit User',
                    null, null);
                return $response->getResponse();
            }

            $role_id = $request->get('role_id');
            $user_id = $request->get('user_id');

            $user = User::find($user_id);
            if (!$user) {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    400, 'User tidak ditemukan',
                    null, null);
                return $response->getResponse();
            }

            $user->role_id = $role_id;
            $user->save();
            DB::commit();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Edit Role User ' . $user->nama, [], null);
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function deleteUser($user_id)
    {
        try {
            DB::beginTransaction();
            $user = User::find($user_id);
            if (!$user) {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    400, 'User tidak ditemukan',
                    null, null);
                return $response->getResponse();
            }

            $user->delete();
            DB::commit();
            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Menghapus User ' . $user->nama, [], null);
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }
}
