<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'walletconnectProjectId' => env('WALLETCONNECT_PROJECT_ID', '')
    ]);
})->name('home');

