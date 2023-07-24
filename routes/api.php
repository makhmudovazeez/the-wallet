<?php

use App\Http\Controllers\User\LoginController as UserLoginController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/registration', [UserLoginController::class, 'registration']);
Route::post('/verify-mail', [UserLoginController::class, 'verifyMail']);

Route::middleware('auth:sanctum')->group(function (Router $router) {
    $router->get('/profile', [UserLoginController::class, 'profile']);
});
