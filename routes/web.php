<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ProductController::class, 'index']);
Route::post('/checkout', [ProductController::class, 'checkout'])->name('checkout');
Route::get('/checkout/success', [ProductController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [ProductController::class, 'cancel'])->name('checkout.cancel');
Route::get('/stripe_webhooks', [ProductController::class, 'webhook'])->name('checkout.webhook');
// stripe.exe listen --forward-to localhost:8000/stripe_webhooks