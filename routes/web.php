<?php

use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinmasController;
use App\Http\Controllers\PayRateController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Row;

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

Route::get('/', function () {
    return view('home');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('linmas', [LinmasController::class, 'index'])->name('linmas.index');
    Route::get('linmas/create', [LinmasController::class, 'create'])->name('linmas.create');
    Route::post('linmas', [LinmasController::class, 'store'])->name('linmas.store');
    Route::get('linmas/{linmas}', [LinmasController::class, 'show'])->name('linmas.show');
    Route::get('linmas/{linmas}/edit', [LinmasController::class, 'edit'])->name('linmas.edit');
    Route::put('linmas/{linmas}', [LinmasController::class, 'update'])->name('linmas.update');
    Route::delete('linmas/{linmas}', [LinmasController::class, 'destroy'])->name('linmas.destroy');



    Route::post('/linmas/import', [LinmasController::class, 'import'])->name('linmas.import');
    // Route::post('/attendance/import', [AttendancesController::class, 'importAttendance'])->name('attendance.import');
    // Route::get('/attendance', [AttendancesController::class, 'index'])->name('attendance.index');

    // Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');

    // Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
    // Route::resource('linmas', LinmasController::class);
    // Route::post('linmas/import', [LinmasController::class, 'import'])->name('linmas.import');

    // Route Attendance
    Route::get('/attendances', [AttendancesController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/import', [AttendancesController::class, 'importAttendance'])->name('attendances.import');
    Route::delete('/attendances/{id}', [AttendancesController::class, 'destroy'])->name('attendances.destroy');

    // Route Payroll
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('payroll/calculate', [PayrollController::class, 'calculatePayroll'])->name('payroll.calculate');
    Route::post('payroll/export-pdf', [PayrollController::class, 'exportPdf'])->name('payroll.exportPdf');
    Route::post('payroll/export-slip/{nik}', [PayrollController::class, 'exportSlip'])->name('payroll.exportSlip');
    Route::post('/payroll/store', [PayrollController::class, 'storePayroll'])->name('payroll.store');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
 