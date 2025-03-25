<?php

namespace App\BusinessLayer;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use App\PresentationLayer\ResponseCreatorPresentationLayer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BukuBusinessLayer
{
    public function addBuku($request)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make(request()->all(), [
                'kategori_id'   => 'required|exists:kategori,kategori_id',
                'nama'          => 'required',
                'isbn'          => 'required',
                'pengarang'     => 'required',
                'sinopsis'      => 'nullable',
                'stok'          => 'nullable',
                'foto'          => 'nullable'
            ], [
                'kategori_id.required'  => 'kategori_id harus diisi',
                'kategori_id.exists'    => 'kategori_id tidak terdaftar',
                'nama.required'         => 'nama harus diisi',
                'isbn.required'         => 'isbn harus diisi',
                'pengarang.required'    => 'pengarang harus diisi',
                'sinopsis.required'     => 'sinopsis harus diisi',
                'stok.required'         => 'stok harus diisi',
                'foto.required'         => 'foto harus diisi',
            ]);

            if ($validation->fails()) {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    401, 'Gagal Validasi Tambah Buku',
                    null, $validation->errors());
                return $response->getResponse();
            }

            $data = $validation->validated();
            if ($request->hasFile('foto')){
                $path = $this->uploadFoto($request->foto);
                $data['foto'] = $path;
            }
            Buku::create($data);

            $kategori = Kategori::findOrFail($request->get('kategori_id'));
            if ($kategori->is_available == 0)
            {
                $kategori->is_available = 1;
                $kategori->save();
            }
            DB::commit();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Menambahkan Buku ' . $data['nama'], [], null);
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function getAllBuku(Request $request)
    {
        try {
            $perPage = $request->get('perPage');

            $buku = Buku::query();

            $data = $perPage ? $buku->paginate($perPage) : $buku->get();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Mengambil Data Buku', $data, null);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function editBuku($request, $id)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make(request()->all(), [
                'kategori_id'   => 'nullable|exists:kategori,kategori_id',
                'nama'          => 'nullable',
                'isbn'          => 'nullable',
                'pengarang'     => 'nullable',
                'sinopsis'      => 'nullable',
                'stok'          => 'nullable',
                'foto'          => 'nullable'
            ], [
                'kategori_id.exists'    => 'kategori_id tidak terdaftar'
            ]);

            if ($validation->fails()) {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    401, 'Gagal Validasi Tambah Buku',
                    null, $validation->errors());
                return $response->getResponse();
            }

            $buku = Buku::findOrFail($id);
            if (!$buku) {
                $response = new ResponseCreatorPresentationLayer(
                    401, 'Data Buku Tidak Ditemukan',
                    null, []);
                return $response->getResponse();
            }

            $data = $validation->validated();
            if ($request->hasFile('foto')){
                if ($buku->foto){
                    $this->deleteFoto($buku->foto);
                }

                $new_path = $this->uploadFoto($request->foto);
                $data['foto'] = $new_path;
            }


            $buku->update($data);
            DB::commit();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Edit Kategori ' . $buku->nama, [], []);
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function deleteBuku($id)
    {
        try {
            $buku = Buku::find($id);
            if (!$buku) {
                $response = new ResponseCreatorPresentationLayer(
                    404, 'Data Buku Tidak Ditemukan',
                    null, []);
                return $response->getResponse();
            }

            $kategori = Kategori::findOrFail($buku->kategori_id);

            if ($buku->foto){
                $this->deleteFoto($buku->foto);
            }
            $buku->delete();


            $is_kategori_has_book = Buku::where('kategori_id', $kategori->kategori_id)->first();
            if(!$is_kategori_has_book)
            {
                $kategori->is_available = 0;
                $kategori->save();
            }
            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Menghapus Buku ' . $buku->nama, [], null);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    private function uploadFoto($file, $folder = 'Buku') {
        if (!empty($file) && $file instanceof UploadedFile) {
            $date = Carbon::now()->format('YmdHisu') . Str::random(6);
            $originalExtension = $file->getClientOriginalExtension();
            $filename = "buku_{$date}.{$originalExtension}";

            $file->storeAs($folder, $filename, 'public');
            $path = $folder . '/' . $filename;
            return $path;
        }
        return '';
    }

    private function deleteFoto($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
