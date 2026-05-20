<?php

use App\Http\Controllers\Api\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api.secret')->patch(
    '/tickets/{code}/use',
    [TicketController::class, 'markAsUsed']
)->name('api.tickets.use');
