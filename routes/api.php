<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceiptController;

Route::middleware('auth:consumer')->group(function () {
    Route::post('/receipt/check', [ReceiptController::class, 'check']);
    Route::post('/receipt/claim', [ReceiptController::class, 'claim']);
});

// Routes for seller system to handle approvals and rejections (should be protected with API key in production)
Route::post('/receipt/approve', [ReceiptController::class, 'handleApproval']);
Route::post('/receipt/reject', [ReceiptController::class, 'handleRejection']);

// Routes for seller/admin system to approve/reject reward redemptions
Route::post('/reward-redemption/approve', [\App\Http\Controllers\RewardRedemptionController::class, 'handleApproval']);
Route::post('/reward-redemption/reject', [\App\Http\Controllers\RewardRedemptionController::class, 'handleRejection']);
