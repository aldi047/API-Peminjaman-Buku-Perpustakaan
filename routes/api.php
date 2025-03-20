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
