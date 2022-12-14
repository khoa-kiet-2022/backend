<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::group([
    // 'middleware' => 'auth:api',
    'middleware' => 'jwt.verify',
    'prefix' => 'auth'
], function ($router) {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
});

// Route::controller(AuthController::class)->group(function () {
//     Route::post('/login', 'login')->name('login');
//     Route::post('/register', 'register')->name('register');
//     Route::get('/profile', 'getProfile')->name('userBio');
// });