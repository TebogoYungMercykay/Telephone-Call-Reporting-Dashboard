<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallReportController;

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

Route::get('/', [CallReportController::class, 'dashboard'])->name('calls.dashboard');
Route::get('/calls/month/{yearMonth}', [CallReportController::class, 'monthlySummary'])->name('calls.monthly');
Route::get('/calls/month/{yearMonth}/extension/{extension}', [CallReportController::class, 'extensionDetails'])->name('calls.details');
