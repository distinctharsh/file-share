<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::get('/', [FileController::class, 'index'])->name('file.index');
Route::post('/upload', [FileController::class, 'upload'])->name('file.upload');
Route::get('/download/{filename}', [FileController::class, 'download'])->name('file.download');
Route::delete('/delete/{filename}', [FileController::class, 'delete'])->name('file.delete');

// Live Chat Without Database Routes
Route::get('/messages', [FileController::class, 'getMessages'])->name('messages.get');
Route::post('/messages', [FileController::class, 'sendMessage'])->name('messages.send');
Route::post('/messages/delete', [App\Http\Controllers\FileController::class, 'deleteMessage'])->name('messages.delete');