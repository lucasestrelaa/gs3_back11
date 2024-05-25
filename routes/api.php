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

Route::get('/home', function(){ return 'Home'; });
//Autenticação
Route::post('/login', [App\Http\Controllers\UserController::class,'index'])->name('index');
Route::post('/logout', [App\Http\Controllers\UserController::class,'logout'])->name('logout');
Route::get('/session', [App\Http\Controllers\UserController::class,'getSession'])->name('getSession');
//Users
Route::post('/usersAll', [App\Http\Controllers\UserController::class,'getUsers'])->name('getUsers');
Route::post('/users', [App\Http\Controllers\UserController::class,'insert'])->name('insert');
Route::get('/user/{token}/{id}', [App\Http\Controllers\UserController::class,'getUser'])->name('getUser');
Route::put('/users', [App\Http\Controllers\UserController::class,'update'])->name('update');
Route::delete('/users/{profile_id}/{id}', [App\Http\Controllers\UserController::class,'delete'])->name('delete');
Route::put('/updateProfileUser', [App\Http\Controllers\UserController::class,'updateProfileUser'])->name('updateProfileUser');
//Profile
Route::get('/profile', [App\Http\Controllers\ProfileController::class,'index'])->name('index');
Route::get('/profile/{token}/{id}', [App\Http\Controllers\ProfileController::class,'profile'])->name('profile');
Route::post('/profile', [App\Http\Controllers\ProfileController::class,'insert'])->name('insert');
Route::put('/profile', [App\Http\Controllers\ProfileController::class,'update'])->name('update');
Route::delete('/profile/{profile_id}/{id}', [App\Http\Controllers\ProfileController::class,'delete'])->name('delete');