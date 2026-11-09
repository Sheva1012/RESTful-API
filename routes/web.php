<?php

use App\Http\Controllers\Web\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebController::class, 'dashboard'])->name('web.dashboard');
Route::get('/assets', [WebController::class, 'assets'])->name('web.assets');
Route::get('/loans', [WebController::class, 'loans'])->name('web.loans');
Route::get('/borrowers', [WebController::class, 'borrowers'])->name('web.borrowers');
Route::get('/docs', [WebController::class, 'docs'])->name('web.docs');
