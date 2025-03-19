<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;

Route::post('encode', [UrlController::class, 'store'])->middleware('throttle:encode');
Route::post('decode', [UrlController::class, 'show'])->middleware('throttle:decode');