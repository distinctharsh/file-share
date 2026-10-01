<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::get('/', [FileController::class, 'index'])->name('home');
Route::post('/upload', [FileController::class, 'upload'])->name('file.upload');
Route::get('/download/{filename}', [FileController::class, 'download'])->name('file.download');