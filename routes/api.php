<?php

use Illuminate\Support\Facades\Route;

$router->get('/check', function () {
    return response()->json(['message' => 'Masook']);
});

Route::group(['prefix' => 'auth'], function ($router){
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::get('logout', 'AuthController@logout');
    Route::get('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
});