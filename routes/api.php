<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProfitGoalsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () { 
    Route::post('/transaction', [TransactionController::class, 'store']);  
    Route::put('/transaction/{id}', [TransactionController::class, 'update']); 
    Route::post('/categories', [CategoryController::class, 'store']);  
    Route::get('/transaction', [TransactionController::class, 'index']);
    Route::delete('/transaction/{id}', [TransactionController::class, 'destroy']);
    Route::get('/transaction/search/{name?}/{type?}', [TransactionController::class, 'show']);
    Route::patch('/admin/remove/{id}', [ManagerController::class, "removeAdmin"]);
    Route::get('/admin', [ManagerController::class, 'getAll']);   
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/categories/search/{name}', [CategoryController::class, 'search']);
    Route::get('categories/restore/{name}', [CategoryController::class, 'restore']);
    Route::get('/categories/getNetProfit/{name}/{frequenc}', [CategoryController::class, 'getNetProfit']);
    Route::post('profit_goal',  [ProfitGoalsController::class, 'store']);
    Route::get('profit_goal',  [ProfitGoalsController::class, 'CalculateProfit']);
    Route::post('/logout', [AuthController::class, 'logout']);
});





 

