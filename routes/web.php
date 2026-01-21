<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', 'App\Http\Controllers\AuthController@webLogin')->name('web.login');
Route::post('/logout', 'App\Http\Controllers\AuthController@webLogout')->name('web.logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/inventaris', function () {
        return view('inventory');
    })->name('inventory');

    Route::get('/users', function () {
        return view('users');
    })->name('users')->middleware('admin');
});
