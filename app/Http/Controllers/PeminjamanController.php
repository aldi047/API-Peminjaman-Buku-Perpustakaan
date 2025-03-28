<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    private $peminjamanBusinessLayer;

    public function __construct()
    {
        $this->peminjamanBusinessLayer = new \App\BusinessLayer\PeminjamanBusinessLayer();
    }

    public function pinjamBuku(Request $request)
    {
        $data = $this->peminjamanBusinessLayer->pinjamBuku($request);
        return response()->json($data, $data['code']);
    }

    public function getPeminjaman(Request $request)
    {
        $data = $this->peminjamanBusinessLayer->getPeminjaman($request);
        return response()->json($data, $data['code']);
    }

    public function getPeminjamanById(Request $request, $id)
    {
        $data = $this->peminjamanBusinessLayer->getPeminjamanById($id);
        return response()->json($data, $data['code']);
    }

    public function kembalikanBuku(Request $request, $id)
    {
        $data = $this->peminjamanBusinessLayer->kembalikanBuku($id);
        return response()->json($data, $data['code']);
    }

    public function cobaPrint()
    {
        $peminjamanList = Peminjaman::with(['detailPeminjam', 'detailPetugas', 'detailBuku'])->get();
        return view('data_print', compact('peminjamanList'));
    }

    public function printDetailPeminjamanPdf(Request $request, $id)
    {
        return $this->peminjamanBusinessLayer->printDetailPeminjamanPdf($id);
    }

    public function printDetailPeminjamanWord(Request $request, $id)
    {
        return $this->peminjamanBusinessLayer->printDetailPeminjamanWord($id);
    }

}
