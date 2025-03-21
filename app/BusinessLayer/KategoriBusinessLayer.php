<?php

namespace App\BusinessLayer;

use App\Models\Kategori;
use App\Models\User;
use App\PresentationLayer\ResponseCreatorPresentationLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KategoriBusinessLayer
{
    public function addKategori($request)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make(request()->all(), [
                'nama'          => 'required|unique:kategori'
            ], [
                'nama.required' => 'Nama harus diisi',
                'nama.unique'   => 'Nama kategori tidak boleh sama',
            ]);

            if ($validation->fails()) {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    401, 'Gagal Validasi Tambah Kategori',
                    null, $validation->errors());
                return $response->getResponse();
            }
            $nama_kategori = $request->get('nama');
            Kategori::create([
                'nama' => $nama_kategori
            ]);
            DB::commit();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Menambahkan Kategori ' . $nama_kategori, [], []);
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function getAllKategori(Request $request)
    {
        try {
            $perPage = $request->get('perPage');

            $kategori = Kategori::query();

            $data = $perPage ? $kategori->paginate($perPage) : $kategori->get();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Mengambil Data Kategori', $data, null);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function editKategori($request, $id)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make(request()->all(), [
                'nama' => 'nullable|unique:kategori,nama,' . $id . ',kategori_id',
                'is_available'  => 'nullable|in:0,1',
            ], [
                'nama.unique'           => 'nama tidak boleh sama',
                'is_available.in'       => 'is_available hanya boleh diisi 1 atau 0'
            ]);

            if ($validation->fails()) {
                DB::rollBack();
                $errors[] = $validation->errors();
                $response = new ResponseCreatorPresentationLayer(
                    401, 'Gagal Validasi Edit Kategori',
                    null, $errors);
                return $response->getResponse();
            }

            $kategori = Kategori::findOrFail($id);

            $kategori->update($request->all());
            DB::commit();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Edit Kategori ' . $kategori->nama, [], []);
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function deleteKategori($id)
    {
        try {
            $kategori = Kategori::findOrFail($id);

            $kategori->delete();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Menghapus Kategori ' . $kategori->nama, [], []);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }
}
