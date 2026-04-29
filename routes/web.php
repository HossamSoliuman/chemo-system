<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\DiagnosisController;
use App\Http\Controllers\Admin\ProtocolController;
use App\Http\Controllers\Admin\DrugController;
use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\Api\ProtocolApiController;
use App\Http\Controllers\Api\OrderCalculationApiController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('diagnoses', DiagnosisController::class);
    Route::resource('protocols', ProtocolController::class);
    Route::resource('drugs', DrugController::class);
});

Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
Route::resource('patients', PatientController::class);

Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
Route::resource('orders', OrderController::class);

Route::prefix('api')->name('api.')->group(function () {
    Route::get('protocols', [ProtocolApiController::class, 'byDiagnosis'])->name('protocols.by_diagnosis');
    Route::get('patients/mrn/{mrn}', [PatientApiController::class, 'findByMrn'])->name('patients.by_mrn');
    Route::post('orders/calculate', [OrderCalculationApiController::class, 'calculate'])->name('orders.calculate');
    Route::get('patients/{patient}/cumulative-doses', [PatientApiController::class, 'cumulativeDoses'])->name('patients.cumulative_doses');
});
