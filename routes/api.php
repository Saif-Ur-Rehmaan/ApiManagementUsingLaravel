<?php

use App\Http\Controllers\ApiManager;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/users', [ApiManager::class, 'GetAllUsers']);

// Create a new user
Route::post('/users/add', [ApiManager::class, 'CreateUser']);
// Retrieve a specific user by ID
Route::get('/users/find', [ApiManager::class, 'GetUserById']);
// Update an existing user
Route::put('/users/update', [ApiManager::class, 'UpdateUser']);
// Delete a user
Route::get('/users/delete', [ApiManager::class, 'DeleteUser']);
 

 