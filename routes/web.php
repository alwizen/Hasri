<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RfidAttendanceController;
use App\Http\Controllers\StudentAttendanceController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/absen', [RfidAttendanceController::class, 'index'])->name('rfid.attendance.form');
Route::post('/absen', [RfidAttendanceController::class, 'store'])->name('rfid.attendance.submit');

Route::get('tap', [StudentAttendanceController::class, 'index'])
    ->name('studentTap');

Route::post('tap', [StudentAttendanceController::class, 'store'])
    ->name('studentTap.store');
