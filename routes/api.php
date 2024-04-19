<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/estudiantes',[StudentController::class, 'index']);

Route::get('/estudiantes/{id}',[StudentController::class, 'show']);

Route::post('/estudiantes', [StudentController::class, 'store']);

Route::put('/estudiantes/{id}',[StudentController::class, 'update']);

Route::patch('/estudiantes/{id}',[StudentController::class, 'updatePartial']);

Route::delete('/estudiantes/{id}', [StudentController::class, 'destroy']);
