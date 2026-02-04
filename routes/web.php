<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\PublicReportController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(PublicReportController::class)->group(function () {
    Route::get('/', 'index')->name('public.map');
    Route::get('/lapor', 'create')->name('public.report.create');
    Route::post('/lapor', 'store')
        ->name('public.report.store')
        ->middleware('throttle:10,1');

    // ✅ route proxy geocode (biar gak kena CORS)
    Route::get('/lapor/geocode', 'geocode')->name('public.geocode')->middleware('throttle:60,1');
});

Route::get('/report/{report}', [PublicReportController::class, 'show'])
    ->name('public.report.show');

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');


    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/data', [ReportController::class, 'data'])
        ->name('reports.data');

    Route::post('/reports/{report}/approve', [ReportController::class, 'approve'])
        ->name('reports.approve');

    Route::post('/reports/{report}/reject', [ReportController::class, 'reject'])
        ->name('reports.reject');

    Route::get('/reports/{report}', [ReportController::class, 'show'])
        ->name('reports.show');
});
