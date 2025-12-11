<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RfidAttendanceController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/absen', [RfidAttendanceController::class, 'index'])->name('rfid.attendance.form');
Route::post('/absen', [RfidAttendanceController::class, 'store'])->name('rfid.attendance.submit');
