<?php

use App\Modules\Payments\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Pago con Stripe (también para invitados)
Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent')->middleware('throttle:20,1');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process')->middleware('throttle:10,1');

// Webhook de Stripe (excluido de CSRF en bootstrap/app.php; exige firma)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');
