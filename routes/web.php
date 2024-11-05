<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', function () {
    return [
        'status'=> false,
        'message'=> 'The user not authenticated, please register'
    ];
})->name('login');
