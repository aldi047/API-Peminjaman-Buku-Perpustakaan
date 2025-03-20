<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    private $kategoriBusinessLayer;

    public function __construct()
    {
        $this->kategoriBusinessLayer = new \App\BusinessLayer\KategoriBusinessLayer();
    }

    public function addKategori(Request $request)
    {
        $data = $this->kategoriBusinessLayer->addKategori($request);
        return response()->json($data, $data['code']);
    }

    public function getAllKategori(Request $request)
    {
        $data = $this->kategoriBusinessLayer->getAllKategori($request);
        return response()->json($data, $data['code']);
    }

    public function editKategori(Request $request, $id)
    {
        $data = $this->kategoriBusinessLayer->editKategori($request, $id);
        return response()->json($data, $data['code']);
    }

    public function deleteKategori(Request $request, $id)
    {
        $data = $this->kategoriBusinessLayer->deleteKategori($id);
        return response()->json($data, $data['code']);
    }
}
