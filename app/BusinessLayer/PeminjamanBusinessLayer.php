<?php

namespace App\BusinessLayer;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\PresentationLayer\ResponseCreatorPresentationLayer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PeminjamanBusinessLayer
{
    public function pinjamBuku($request)
    {
        try {
            DB::beginTransaction();
            $validation = Validator::make($request->all(), [
                'peminjam_user_id'          => 'required|exists:users,user_id',
                'petugas_user_id'           => 'required|exists:users,user_id',
                'buku_id'                   => 'required|exists:buku,buku_id',
                'durasi_peminjaman_in_days' => 'required|integer|max:3'
            ], [
                'peminjam_user_id.required' => 'peminjam_user_id harus diisi',
                'peminjam_user_id.exists'   => 'peminjam_user_id tidak ditemukan',
                'petugas_user_id.required'  => 'petugas_user_id harus diisi',
                'petugas_user_id.exists'    => 'petugas_user_id tidak ditemukan',
                'buku_id.required'          => 'buku_id harus diisi',
                'buku_id.exists'            => 'buku_id tidak ditemukan',
                'durasi_peminjaman_in_days.required'  => 'durasi_peminjaman_in_days harus diisi',
                'durasi_peminjaman_in_days.integer'   => 'durasi_peminjaman_in_days harus dalam integer',
                'durasi_peminjaman_in_days.max'       => 'durasi_peminjaman_in_days maksimal 3 hari'
            ]);

            if ($validation->fails()) {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    401, 'Gagal Validasi Tambah Kategori',
                    null, $validation->errors());
                return $response->getResponse();
            }

            $buku = Buku::findOrFail($request->buku_id);
            if($buku->stok == 0)
            {
                DB::rollBack();
                $response = new ResponseCreatorPresentationLayer(
                    200, 'Stok Buku Habis',
                    [], null);
                return $response->getResponse();
            }

            $buku->stok--;
            $buku->save();

            Peminjaman::create($validation->validated());
            DB::commit();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Menambahkan Peminjaman', [], null);
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function getPeminjaman($request)
    {
        try {
            $perPage = $request->get('perPage');

            $buku = Peminjaman::query();

            $data = $perPage ? $buku->paginate($perPage) : $buku->get();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Mengambil Data Peminjaman', $data, null);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function getPeminjamanById($id)
    {
        try {
            $peminjaman = Peminjaman::find($id);
            if (!$peminjaman) {
                $response = new ResponseCreatorPresentationLayer(
                    404, 'Data Peminjaman Tidak Ditemukan',
                    null, []);
                return $response->getResponse();
            }

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Mengambil Data Peminjaman', $peminjaman, null);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }

    public function kembalikanBuku($id)
    {
        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::findOrFail($id);

            $waktu_pengembalian = Carbon::now();
            $durasi_pengembalian_user = $peminjaman->waktu_peminjaman->diffInDays($waktu_pengembalian);
            $pengembalian_in_days = $peminjaman->durasi_peminjaman_in_days - $durasi_pengembalian_user;

            $total_denda = 0;
            $total_keterlambatan = 0;

            if($pengembalian_in_days < 0){
                $total_keterlambatan = abs($pengembalian_in_days);
                $total_denda = 1000 * $total_keterlambatan;
            }
            $peminjaman->update([
                'waktu_pengembalian'            => $waktu_pengembalian,
                'total_keterlambatan_in_days'   => $total_keterlambatan,
                'total_denda'                   => $total_denda
            ]);

            $buku = Buku::findOrFail($peminjaman->buku_id);
            $buku->stok++;
            $buku->save();
            DB::commit();

            $response = new ResponseCreatorPresentationLayer(
                200, 'Berhasil Mengembalikan Buku ', [], []);
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors));
        }
        return $response->getResponse();
    }
}
