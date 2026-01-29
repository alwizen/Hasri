<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RfidAttendanceController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/absen/guru', [RfidAttendanceController::class, 'index'])->name('rfid.attendance.form');
Route::post('/absen/guru', [RfidAttendanceController::class, 'store'])->name('rfid.attendance.submit');

Route::get('absen/siswa', [StudentAttendanceController::class, 'index'])
    ->name('studentTap');

Route::post('absen/siswa', [StudentAttendanceController::class, 'store'])
    ->name('studentTap.store');

Route::get('/siswa', [StudentController::class, 'create'])->name('siswa.create');
Route::post('/siswa', [StudentController::class, 'store'])->name('siswa.store');
