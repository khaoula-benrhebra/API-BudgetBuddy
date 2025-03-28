<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\TagController;


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

Route::post('/register',[App\Http\Controllers\API\AuthController::class,'register']);
Route::post('/login',[App\Http\Controllers\API\AuthController::class,'login']);

Route::group(['middleware' => ['auth:sanctum']],function(){
    Route::post('/logout',[App\Http\Controllers\API\AuthController::class,'logout']);
   
});

Route::middleware('auth:sanctum')->prefix('expenses')->group(function () {
    Route::post('/', [ExpenseController::class, 'create']);
    Route::put('{expense}', [ExpenseController::class, 'update']);
    Route::delete('{expense}', [ExpenseController::class, 'destroy']);
    Route::post('{expense}/tags', [ExpenseController::class, 'attachTagsToExpense']);
    Route::get('/', [ExpenseController::class, 'index']);
    Route::get('{expense}', [ExpenseController::class, 'show']);


  
});

Route::middleware('auth:sanctum')->prefix('tags')->group(function () {
    Route::post('/', [TagController::class, 'create']);
    Route::put('{tag}', [TagController::class, 'update']);
    Route::delete('{tag}', [TagController::class, 'destroy']);
    Route::get('/', [TagController::class, 'index']);
    Route::get('{tag}', [TagController::class, 'show']);
});

///groupes

use App\Http\Controllers\GroupController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/groups', [GroupController::class, 'index']);
    Route::get('/groups/{id}', [GroupController::class, 'show']);
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']);
});




//expenses partagées routes


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/groups/{groupId}/expenses', [ExpenseController::class, 'addGroupExpense']);
    Route::get('/groups/{groupId}/expenses', [ExpenseController::class, 'listGroupExpenses']);
    Route::delete('/groups/{groupId}/expenses/{expenseId}', [ExpenseController::class, 'deleteGroupExpense']);
});



