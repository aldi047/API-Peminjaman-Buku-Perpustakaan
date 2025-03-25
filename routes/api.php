<?php

use Illuminate\Support\Facades\Route;

$router->get('/check', function () {
    return response()->json(['message' => 'Masook']);
});

Route::group(['prefix' => 'auth'], function (){
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::group(['middleware' => 'auth:api'], function (){
        Route::get('logout', 'AuthController@logout');
        Route::get('refresh', 'AuthController@refresh');
        Route::get('me', 'AuthController@me');
        Route::get('delete', 'AuthController@delete');
    });
});

Route::group(['prefix' => 'user'], function (){
    Route::get('/', 'UserController@getAllUsers');
    Route::post('/edit', 'UserController@editUser');
    Route::delete('/delete/{user_id}', 'UserController@deleteUser');
});

Route::group(['prefix' => 'kategori'], function (){
    Route::get('/', 'KategoriController@getAllKategori');
    Route::post('/tambah', 'KategoriController@addKategori');
    Route::post('/{id}/edit', 'KategoriController@editKategori');
    Route::delete('/{id}/delete', 'KategoriController@deleteKategori');
});

Route::group(['prefix' => 'buku'], function (){
    Route::get('/', 'BukuController@getAllBuku');
    Route::post('/tambah', 'BukuController@addBuku');
    Route::post('/{id}/edit', 'BukuController@editBuku');
    Route::delete('/{id}/delete', 'BukuController@deleteBuku');
});

Route::group(['prefix' => 'peminjaman'], function (){
    Route::get('/', 'PeminjamanController@getPeminjaman');
    Route::post('/', 'PeminjamanController@pinjamBuku');
    Route::get('/{id}', 'PeminjamanController@getPeminjamanById');
    Route::get('/{id}/kembalikan', 'PeminjamanController@kembalikanBuku');
});

Route::get('/files', 'BukuController@getFile');
