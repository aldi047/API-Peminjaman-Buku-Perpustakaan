<?php

namespace App\Http\Controllers;

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

}
