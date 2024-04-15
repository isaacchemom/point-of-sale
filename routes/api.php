<?php

use App\Http\Controllers\MpesaController;
use App\Http\Controllers\SellPosController;
use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});





Route::post('v1/stk/push', [MpesaController::class,'stkPushRequest']);
Route::post('v1/confirmation', [MpesaController::class,'handleMpesaConfirmation']);
Route::post('v1/access/token', [MpesaController::class,'generateAccessToken']);
Route::post('v1/mpesa/response', [MpesaController::class,'processPayment']);
Route::post('v1/mpesa/transactions', [MpesaController::class,'getMpesaTransactions']);
Route::post('v1/mpesa/transaction/cancel', [MpesaController::class,'cancelTransaction']);
Route::post('v1/mpesa/transaction/complete', [MpesaController::class,'completeTransaction']);
Route::post('/pos/save', [SellPosController::class, 'storePos']);
//changed


