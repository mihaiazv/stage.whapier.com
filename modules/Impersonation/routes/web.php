<?php

use Illuminate\Support\Facades\Route;
use Modules\Impersonation\Controllers\ImpersonationController;

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/impersonate/{organization}', [ImpersonationController::class, 'impersonate'])->name('admin.impersonate');
    Route::get('/impersonate-stop', [ImpersonationController::class, 'stop'])->name('admin.impersonate.stop');
});
