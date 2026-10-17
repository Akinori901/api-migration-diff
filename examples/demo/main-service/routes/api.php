<?php

use App\Http\Controllers\OrderListController;
use Illuminate\Support\Facades\Route;

// 新システム（切り出した注文サービス）。Bearer トークン認証。
Route::middleware('auth:sanctum')->group(function () {
    Route::post('orders/list', OrderListController::class);
});
