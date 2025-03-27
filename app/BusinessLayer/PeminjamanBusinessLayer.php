<?php

namespace App\BusinessLayer;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\PresentationLayer\ResponseCreatorPresentationLayer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

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

            $buku = Peminjaman::with(['detailPeminjam', 'detailPetugas', 'detailBuku']);

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

    public function printDetailPeminjamanPdf($id)
    {
        try {
            $peminjaman = Peminjaman::with(['detailPeminjam', 'detailPetugas', 'detailBuku'])
                            ->where('peminjaman_id', $id)->get();
            $pdf = Pdf::loadView('data_print', ['peminjamanList' => $peminjaman]);
            return $pdf->download('Laporan Peminjaman.pdf');

        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors))->getResponse();
            return response()->json($response, $response['code']);
        }
    }

    public function printDetailPeminjamanWord($id)
    {
        try {
            $peminjaman = Peminjaman::with(['detailPeminjam', 'detailPetugas', 'detailBuku'])
                            ->where('peminjaman_id', $id)->first();

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $section->addText('Laporan Peminjaman');

            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 50]);

            $table->addRow();
            $table->addCell(900, ['gridSpan' => 3])->addText(
                "Peminjaman ID: " . $peminjaman->peminjaman_id, ['bold' => true]
            );

            $table->addRow();
            $cel_peminjam = $table->addCell(3000);
            $cel_peminjam->addText("Peminjam:");
            $cel_peminjam->addText($peminjaman->detailPeminjam->nama);
            $cel_peminjam->addText($peminjaman->detailPeminjam->email);

            $cel_petugas = $table->addCell(3000);
            $cel_petugas->addText("Petugas:");
            $cel_petugas->addText($peminjaman->detailPetugas->nama);
            $cel_petugas->addText($peminjaman->detailPetugas->email);

            $cel_durasi = $table->addCell(3000);
            $cel_durasi->addText("Durasi Peminjaman:");
            $cel_durasi->addText("{$peminjaman->durasi_peminjaman_in_days} Hari");

            $table->addRow();
            $cel_peminjaman = $table->addCell(3000);
            $cel_peminjaman->addText("Waktu Peminjaman:");
            $cel_peminjaman->addText($peminjaman->waktu_peminjaman);

            $cel_pengembalian = $table->addCell(3000);
            $cel_pengembalian->addText("Waktu Pengembalian:");
            $cel_pengembalian->addText($peminjaman->waktu_pengembalian);

            $cel_denda = $table->addCell(3000);
            $cel_denda->addText("Total Denda: Rp " . number_format($peminjaman->total_denda, 0, ',', '.'));
            $cel_denda->addText("Total Keterlambatan: {$peminjaman->total_keterlambatan_in_days} Hari");

            $table->addRow();
            $buku_row = $table->addCell(2000, ['gridSpan' => 2]);
            $buku_row->addText(
                "Buku: {$peminjaman->detailBuku->nama}"
            );
            $buku_row->addText("ISBN: {$peminjaman->detailBuku->isbn}");
            $buku_row->addText("Pengarang: {$peminjaman->detailBuku->pengarang}");
            $buku_row->addText("Sinopsis: {$peminjaman->detailBuku->sinopsis}");

            $bukuFotoPath = storage_path("app/public/{$peminjaman->detailBuku->foto}");
            if (file_exists($bukuFotoPath)) {
                $table->addCell(1000)->addImage($bukuFotoPath, [
                    'width' => 100,
                    'height' => 100,
                    'alignment' => 'center'
                ]);
            } else {
                $table->addCell(4000)->addText("No Image");
            }

            // $section->addTextBreak(2);

            $tempFile = tempnam(sys_get_temp_dir(), 'word');
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);

            return response()->download($tempFile, 'Laporan Peminjaman.docx')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $response = (new ResponseCreatorPresentationLayer(500, 'Terjadi kesalahan pada server', [], $errors))->getResponse();
            return response()->json($response, $response['code']);
        }
    }
}
