<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/sanctum/csrf-cookie', function (\Illuminate\Http\Request $request) {
    return response()->json(['message' => 'CSRF cookie route is active']);
});