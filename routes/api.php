<?php

use App\Http\Controllers\ApiManager;
use App\Http\Controllers\FranchisApiManager;
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

Route::group(['prefix' => '/users'], function () {
    // Get all users
    Route::get('/', [ApiManager::class, 'GetAllUsers']);
    
    // Change password
    Route::post('/ChangePassword', [ApiManager::class, 'ChangePassword']);
    
    // Forgot password
    Route::post('/ForgotPass', [ApiManager::class, 'ForgotPassword']);
    
    // Login user
    Route::post('/login', [ApiManager::class, 'LoginUser']);
    
    // Create a new user
    Route::post('/add', [ApiManager::class, 'CreateUser']);
    
    // Retrieve a specific user by ID
    Route::get('/find', [ApiManager::class, 'GetUserById']);
    
    // Update an existing user
    Route::put('/update', [ApiManager::class, 'UpdateUser']);
    
    // Delete a user
    Route::delete('/delete/{id}', [ApiManager::class, 'DeleteUser']);
});
Route::group(['prefix' => '/franchises'],function (){
    Route::get('/',[FranchisApiManager::class,'GetAllFranchise']);

    Route::post('/MenuAndSeasonalCat',[FranchisApiManager::class,'GetAvailableMenuAndSeasonalCat']);//give franchise id
    Route::post('/ItemsOfCategory',[FranchisApiManager::class,'GetAvailableItemsOfCategory']);//give categoryid
    Route::post('/GetSizesOfItem',[FranchisApiManager::class,'GetSizesOfItem']);//give the SizeOptionsIdArray fetched from /ItemsOfCategory which contain size id available
    
   
    Route::post('/GetOrderOfOrderNumber',[FranchisApiManager::class,'GetOrderOfOrderNumber']);//give the SizeOptionsIdArray fetched from /ItemsOfCategory which contain size id available
    
    Route::put('/InsertOrder',[FranchisApiManager::class,'InsertOrder']);
});
 