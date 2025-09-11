<?php

use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinmasController;
use App\Http\Controllers\LinmasLoginController;
use App\Http\Controllers\PayRateController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SalaryRateController;
use App\Http\Controllers\AllowanceDeductionController;
use App\Http\Controllers\Auth\PerangkatLoginController;
use App\Http\Controllers\MonthClosingController;
use App\Http\Controllers\PayrollWorkflowController;
use App\Http\Controllers\PerangkatDashboardController;
use App\Http\Controllers\PositionSalaryRateController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/home', function () {
    if (auth()->check()) {
        return auth()->user()->hasRole('perangkat_desa')
            ? redirect()->route('perangkat.dashboard')
            : redirect()->route('dashboard');
    }
    return redirect('/');
})->name('home.redirect');

// Rute untuk semua user terautentikasi
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rute khusus Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // User Management
    Route::resource('users', UserManagementController::class);

    // Templates
    Route::get('/templates/linmas', [TemplateController::class, 'downloadLinmasTemplate'])->name('templates.linmas');
    Route::get('/templates/attendance', [TemplateController::class, 'downloadAttendanceTemplate'])->name('templates.attendance');

    // Linmas

    Route::get('linmas', [LinmasController::class, 'index'])->name('linmas.index');
    Route::get('linmas/create', [LinmasController::class, 'create'])->name('linmas.create');
    Route::post('linmas', [LinmasController::class, 'store'])->name('linmas.store');
    Route::get('linmas/{linmas}/edit', [LinmasController::class, 'edit'])->name('linmas.edit');
    Route::put('linmas/{linmas}', [LinmasController::class, 'update'])->name('linmas.update');
    Route::delete('linmas/{linmas}', [LinmasController::class, 'destroy'])->name('linmas.destroy');
    Route::get('linmas/{linmas}', [LinmasController::class, 'show'])->name('linmas.show');
    // Route::resource('linmas', LinmasController::class);
    Route::post('/linmas/import', [LinmasController::class, 'import'])->name('linmas.import');
    
    // Route::resource('linmas', LinmasController::class)->except(['show']);
    // Route::get('linmas/{linmas}', [LinmasController::class, 'show'])->name('linmas.show');
    // Route::post('/linmas/import', [LinmasController::class, 'import'])->name('linmas.import');

    // Attendances
    Route::get('/attendances', [AttendancesController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/import', [AttendancesController::class, 'importAttendance'])->name('attendances.import');
    Route::delete('/attendances/{id}', [AttendancesController::class, 'destroy'])->name('attendances.destroy');

    // Payroll
     Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::post('/calculate', [PayrollController::class, 'calculatePayroll'])->name('calculate');
        Route::post('/export-pdf', [PayrollController::class, 'exportPdf'])->name('exportPdf');
        Route::get('/export-pdf', [PayrollController::class, 'exportPdf'])->name('exportPdfGet'); // Tambah route GET untuk export PDF
        Route::post('/export-slip/{nik?}', [PayrollController::class, 'exportSlip'])->name('exportSlip'); // Buat parameter nik opsional
        Route::get('/export-slip/{id}', [PayrollController::class, 'exportSlip'])->name('exportSlipGet'); // Tambah route GET untuk export slip
        Route::post('/store', [PayrollController::class, 'storePayroll'])->name('store');
    });

    // Payroll History
    Route::prefix('payroll-history')->name('payroll.history.')->group(function () {
        Route::get('/', [PayrollHistoryController::class, 'index'])->name('index');
        Route::put('/{payroll}/status', [PayrollHistoryController::class, 'updateStatus'])->name('update-status');
        Route::post('/monthly-report', [PayrollHistoryController::class, 'monthlyReport'])->name('monthly-report');
    });

    // Month Closing
    Route::resource('month-closing', MonthClosingController::class)->except(['destroy']);
    Route::put('month-closing/{monthClosing}/reopen', [MonthClosingController::class, 'reopen'])->name('month-closing.reopen');
    Route::get('month-closing/{monthClosing}/report', [MonthClosingController::class, 'generateReport'])->name('month-closing.generate-report');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        // Salary Rates
        Route::resource('rates', SalaryRateController::class)->names([
            'index' => 'rates.index',
            'create' => 'rates.create',
            'store' => 'rates.store',
            'edit' => 'rates.edit',
            'update' => 'rates.update',
            'destroy' => 'rates.destroy'
        ]);

        // Position Salary Rates
        Route::resource('position-rates', PositionSalaryRateController::class);
        Route::get('position-rates-recalculate', [PositionSalaryRateController::class, 'recalculateRates'])
            ->name('position-rates.recalculate');



        // Linmas Assignments
        Route::get('allowances-deductions/linmas-assignments', [AllowanceDeductionController::class, 'linmasAssignments'])
            ->name('allowances-deductions.linmas-assignments');
        Route::get('allowances-deductions/linmas/{linmas}/assign', [AllowanceDeductionController::class, 'showAssignForm'])
            ->name('allowances-deductions.assign');
        Route::post('allowances-deductions/linmas/{linmas}/assign', [AllowanceDeductionController::class, 'saveAssignments'])
            ->name('allowances-deductions.save-assignments');
        // Allowances & Deductions
        Route::resource('allowances-deductions', AllowanceDeductionController::class)->names([
            'index' => 'allowances-deductions.index',
            'create' => 'allowances-deductions.create',
            'store' => 'allowances-deductions.store',
            'edit' => 'allowances-deductions.edit',
            'update' => 'allowances-deductions.update',
            'destroy' => 'allowances-deductions.destroy'
        ])->parameters([
            'allowances-deductions' => 'allowanceDeduction'
        ]);
    });

    // Payroll Workflow
    Route::prefix('payroll-workflow')->name('payroll.workflow.')->group(function () {
        Route::get('/', [PayrollWorkflowController::class, 'index'])->name('index');
        Route::get('/report', [PayrollWorkflowController::class, 'report'])->name('report');
        Route::get('/{payroll}', [PayrollWorkflowController::class, 'show'])->name('show');
        Route::put('/{payroll}/status', [PayrollWorkflowController::class, 'updateStatus'])->name('update-status');
    });

    // Linmas Login Management
    Route::prefix('admin/linmas-login')->name('admin.linmas-login.')->group(function () {
        Route::get('/', [LinmasLoginController::class, 'index'])->name('index');
        Route::get('/create/{linmas}', [LinmasLoginController::class, 'create'])->name('create');
        Route::post('/store/{linmas}', [LinmasLoginController::class, 'store'])->name('store');
        Route::get('/edit/{linmas}', [LinmasLoginController::class, 'edit'])->name('edit');
        Route::put('/update/{linmas}', [LinmasLoginController::class, 'update'])->name('update');
        Route::delete('/destroy/{linmas}', [LinmasLoginController::class, 'destroy'])->name('destroy');
        Route::patch('/toggle/{linmas}', [LinmasLoginController::class, 'toggle'])->name('toggle');
        Route::post('/generate-password/{linmas}', [LinmasLoginController::class, 'generatePassword'])->name('generate-password');
    });
});

// Rute Login Perangkat Desa
Route::middleware('guest:perangkat')->group(function () {
    Route::get('/perangkat/login', [PerangkatLoginController::class, 'showLoginForm'])->name('perangkat.login');
    Route::post('/perangkat/login', [PerangkatLoginController::class, 'login']);
});

// Rute Logout Perangkat Desa
Route::post('/perangkat/logout', [PerangkatLoginController::class, 'logout'])
    ->middleware('auth:perangkat')
    ->name('perangkat.logout');

// Rute khusus Perangkat Desa
Route::middleware('auth:perangkat')->group(function () {
    Route::get('/perangkat/dashboard', [PerangkatDashboardController::class, 'index'])->name('perangkat.dashboard');
    Route::get('/perangkat/profile', [PerangkatDashboardController::class, 'profile'])->name('perangkat.profile');
    Route::put('/perangkat/profile', [PerangkatDashboardController::class, 'updateProfile'])->name('perangkat.profile.update');
    Route::put('/perangkat/password', [PerangkatDashboardController::class, 'updatePassword'])->name('perangkat.password.update');
    Route::get('/perangkat/attendances', [PerangkatDashboardController::class, 'attendances'])->name('perangkat.attendances');
    Route::get('/perangkat/payrolls', [PerangkatDashboardController::class, 'payrolls'])->name('perangkat.payrolls');
    Route::get('/perangkat/payrolls/{payroll}', [PerangkatDashboardController::class, 'payrollDetail'])->name('perangkat.payroll-detail');
    Route::get('/perangkat/month-closing', [PerangkatDashboardController::class, 'monthClosing'])->name('perangkat.month-closing');
});

require __DIR__ . '/auth.php';
