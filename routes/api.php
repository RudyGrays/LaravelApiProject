<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileUploadController;




Route::get('register', function(){
    return response(['okay'=>'ok']);
});
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::post('files', [FileUploadController::class, 'uploadFiles'])->middleware('auth:sanctum');
Route::post('files/{id}', [FileUploadController::class, 'updateFile'])->middleware('auth:sanctum');
Route::delete('files/{id}', [FileUploadController::class, 'deleteFile'])->middleware('auth:sanctum');