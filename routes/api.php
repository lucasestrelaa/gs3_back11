<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/home', function(){ return 'Home'; });
//users/login
Route::post('/login', [App\Http\Controllers\UserController::class,'index'])->name('index');
Route::get('/logout', [App\Http\Controllers\UserController::class,'logout'])->name('logout');
Route::get('/session', [App\Http\Controllers\UserController::class,'getSession'])->name('getSession');
Route::post('/usersAll', [App\Http\Controllers\UserController::class,'getUsers'])->name('getUsers');
Route::post('/users', [App\Http\Controllers\UserController::class,'insert'])->name('insert');
Route::get('/user/{token}/{id}', [App\Http\Controllers\UserController::class,'getUser'])->name('getUser');
Route::put('/users', [App\Http\Controllers\UserController::class,'update'])->name('update');
Route::delete('/users', [App\Http\Controllers\UserController::class,'delete'])->name('delete');
Route::put('/updateProfileUser', [App\Http\Controllers\UserController::class,'updateProfileUser'])->name('updateProfileUser');

//
Route::get('/profile', [App\Http\Controllers\ProfileController::class,'index'])->name('index');
Route::get('/profile/profile', [App\Http\Controllers\ProfileController::class,'profile'])->name('profile');