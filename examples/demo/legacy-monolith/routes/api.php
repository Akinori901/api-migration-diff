<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// 旧システム（モノリス）。セッション認証。
Route::middleware('auth')->group(function () {
    Route::post('order/order_list', [OrderController::class, 'order_list']);
});
