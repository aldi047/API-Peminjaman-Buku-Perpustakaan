<?php

namespace App\Http\Controllers;

use App\PresentationLayer\ResponseCreatorPresentationLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    private $bukuBusinessLayer;

    public function __construct()
    {
        $this->bukuBusinessLayer = new \App\BusinessLayer\BukuBusinessLayer();
    }

    public function addBuku(Request $request)
    {
        $data = $this->bukuBusinessLayer->addBuku($request);
        return response()->json($data, $data['code']);
    }

    public function getAllBuku(Request $request)
    {
        $data = $this->bukuBusinessLayer->getAllBuku($request);
        return response()->json($data, $data['code']);
    }

    public function editBuku(Request $request, $id)
    {
        $data = $this->bukuBusinessLayer->editBuku($request, $id);
        return response()->json($data, $data['code']);
    }

    public function deleteBuku(Request $request, $id)
    {
        $data = $this->bukuBusinessLayer->deleteBuku($id);
        return response()->json($data, $data['code']);
    }

    public function getFile(Request $request)
    {
        $fullPath = $request->get('name');

        if (Storage::disk('public')->exists($fullPath)) {
            return response()->file(storage_path("app/public/{$fullPath}"));
        }

        $response = new ResponseCreatorPresentationLayer(
            400, 'File tidak ditemukan',
            null, []);
        return $response->getResponse();
    }
}
