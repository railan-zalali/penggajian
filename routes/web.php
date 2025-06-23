<?php

use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinmasController;
use App\Http\Controllers\PayRateController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SalaryRateController;
use App\Http\Controllers\AllowanceDeductionController;
use App\Http\Controllers\MonthClosingController;
use App\Http\Controllers\PayrollWorkflowController;
use App\Http\Controllers\PerangkatDashboardController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

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

require __DIR__ . '/auth.php';

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // User Management Routes
    Route::resource('users', UserManagementController::class);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Template Routes
    Route::get('/templates/linmas', [TemplateController::class, 'downloadLinmasTemplate'])->name('templates.linmas');
    Route::get('/templates/attendance', [TemplateController::class, 'downloadAttendanceTemplate'])->name('templates.attendance');

    // Linmas Routes
    Route::get('linmas', [LinmasController::class, 'index'])->name('linmas.index');
    Route::get('linmas/create', [LinmasController::class, 'create'])->name('linmas.create');
    Route::post('linmas', [LinmasController::class, 'store'])->name('linmas.store');
    Route::get('linmas/{linmas}', [LinmasController::class, 'show'])->name('linmas.show');
    Route::get('linmas/{linmas}/edit', [LinmasController::class, 'edit'])->name('linmas.edit');
    Route::put('linmas/{linmas}', [LinmasController::class, 'update'])->name('linmas.update');
    Route::delete('linmas/{linmas}', [LinmasController::class, 'destroy'])->name('linmas.destroy');
    Route::post('/linmas/import', [LinmasController::class, 'import'])->name('linmas.import');

    // Attendance Routes
    Route::get('/attendances', [AttendancesController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/import', [AttendancesController::class, 'importAttendance'])->name('attendances.import');
    Route::delete('/attendances/{id}', [AttendancesController::class, 'destroy'])->name('attendances.destroy');

    // Payroll Routes
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('payroll/calculate', [PayrollController::class, 'calculatePayroll'])->name('payroll.calculate');
    Route::post('payroll/export-pdf', [PayrollController::class, 'exportPdf'])->name('payroll.exportPdf');
    Route::post('payroll/export-slip/{nik}', [PayrollController::class, 'exportSlip'])->name('payroll.exportSlip');
    Route::post('/payroll/store', [PayrollController::class, 'storePayroll'])->name('payroll.store');

    // Payroll History Routes
    Route::get('payroll/history', [PayrollHistoryController::class, 'index'])->name('payroll.history');
    Route::put('payroll/history/{payroll}/status', [PayrollHistoryController::class, 'updateStatus'])->name('payroll.update-status');
    Route::post('payroll/monthly-report', [PayrollHistoryController::class, 'monthlyReport'])->name('payroll.monthly-report');

    // Month Closing Routes
    Route::get('month-closing', [MonthClosingController::class, 'index'])->name('month-closing.index');
    Route::get('month-closing/create', [MonthClosingController::class, 'create'])->name('month-closing.create');
    Route::post('month-closing', [MonthClosingController::class, 'store'])->name('month-closing.store');
    Route::get('month-closing/{monthClosing}', [MonthClosingController::class, 'show'])->name('month-closing.show');
    Route::put('month-closing/{monthClosing}/reopen', [MonthClosingController::class, 'reopen'])->name('month-closing.reopen');
    Route::get('month-closing/{monthClosing}/report', [MonthClosingController::class, 'generateReport'])->name('month-closing.generate-report');

    // Settings Routes
    // Salary Rates
    Route::get('settings/rates', [SalaryRateController::class, 'index'])->name('settings.rates.index');
    Route::get('settings/rates/create', [SalaryRateController::class, 'create'])->name('settings.rates.create');
    Route::post('settings/rates', [SalaryRateController::class, 'store'])->name('settings.rates.store');
    Route::get('settings/rates/{rate}/edit', [SalaryRateController::class, 'edit'])->name('settings.rates.edit');
    Route::put('settings/rates/{rate}', [SalaryRateController::class, 'update'])->name('settings.rates.update');
    Route::delete('settings/rates/{rate}', [SalaryRateController::class, 'destroy'])->name('settings.rates.destroy');

    // Allowances & Deductions
    Route::get('settings/allowances-deductions', [AllowanceDeductionController::class, 'index'])->name('settings.allowances-deductions.index');
    Route::get('settings/allowances-deductions/create', [AllowanceDeductionController::class, 'create'])->name('settings.allowances-deductions.create');
    Route::post('settings/allowances-deductions', [AllowanceDeductionController::class, 'store'])->name('settings.allowances-deductions.store');
    Route::get('settings/allowances-deductions/{allowanceDeduction}/edit', [AllowanceDeductionController::class, 'edit'])->name('settings.allowances-deductions.edit');
    Route::put('settings/allowances-deductions/{allowanceDeduction}', [AllowanceDeductionController::class, 'update'])->name('settings.allowances-deductions.update');
    Route::delete('settings/allowances-deductions/{allowanceDeduction}', [AllowanceDeductionController::class, 'destroy'])->name('settings.allowances-deductions.destroy');

    // Linmas Allowances & Deductions Assignments
    Route::get('settings/allowances-deductions/linmas-assignments', [AllowanceDeductionController::class, 'linmasAssignments'])->name('settings.allowances-deductions.linmas-assignments');
    Route::get('settings/allowances-deductions/linmas/{linmas}/assign', [AllowanceDeductionController::class, 'showAssignForm'])->name('settings.allowances-deductions.assign');
    Route::post('settings/allowances-deductions/linmas/{linmas}/assign', [AllowanceDeductionController::class, 'saveAssignments'])->name('settings.allowances-deductions.save-assignments');

    // Payroll Workflow Routes
    Route::prefix('payroll/workflow')->name('payroll.workflow.')->group(function () {
        Route::get('/', [PayrollWorkflowController::class, 'index'])->name('index');
        Route::get('/report', [PayrollWorkflowController::class, 'report'])->name('report');
        Route::get('/{payroll}', [PayrollWorkflowController::class, 'show'])->name('show');
        Route::put('/{payroll}/status', [PayrollWorkflowController::class, 'updateStatus'])->name('update-status');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Perangkat Desa Routes
Route::middleware(['auth', 'role:perangkat_desa'])->prefix('perangkat')->name('perangkat.')->group(function () {
    Route::get('/dashboard', [PerangkatDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [PerangkatDashboardController::class, 'profile'])->name('profile');
    Route::get('/attendances', [PerangkatDashboardController::class, 'attendances'])->name('attendances');
    Route::get('/payrolls', [PerangkatDashboardController::class, 'payrolls'])->name('payrolls');
    Route::get('/payrolls/{payroll}', [PerangkatDashboardController::class, 'payrollDetail'])->name('payroll-detail');
});

// Update route login untuk redirect berdasarkan role
Route::get('/home', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'perangkat_desa') {
            return redirect()->route('perangkat.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
    return redirect('/');
})->name('home.redirect');
