<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AIInsightController;
use App\Http\Controllers\ApplicationLeaveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceImportController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ContributionVersionController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\EmployeeContributionSettingsController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\EmployeeRole\ApplicationLeaveController as EmployeeApplicationLeaveController;
use App\Http\Controllers\HrRole\HRAIInsightController;
use App\Http\Controllers\HrRole\HRAttendanceImportController;
use App\Http\Controllers\HrRole\HRApplicationLeaveController;
use App\Http\Controllers\HrRole\HRAttendanceController;
use App\Http\Controllers\HrRole\HRBranchController;
use App\Http\Controllers\HrRole\HRContributionVersionController;
use App\Http\Controllers\HrRole\HRDashboardController;
use App\Http\Controllers\HrRole\HRDeductionController;
use App\Http\Controllers\HrRole\HREmployeeController;
use App\Http\Controllers\HrRole\HRIncentiveController;
use App\Http\Controllers\HrRole\HRPositionController;
use App\Http\Controllers\HrRole\PayrollController as HrPayrollController;
use App\Http\Controllers\HrRole\PayrollPeriodController as HrPayrollPeriodController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Middleware\CapPerpageMiddleware;
use App\Http\Middleware\HomeMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    if (app()->isProduction()) {
        Inertia::clearHistory();
    }
    return Inertia::render('auth/login');
})->name('home')->middleware(HomeMiddleware::class);

// ─── Admin routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin', 'auth.session', 'throttle:limit-actions', CapPerpageMiddleware::class . ':100'])->group(function () {

    Route::get('payroll', function () {
        return Inertia::render('payroll/index');
    });

    // Admin payroll email routes
    Route::post('/payrolls/{payroll}/email', [PayrollController::class, 'emailPayroll'])->name('payrolls.email');
    Route::post('/payrolls/bulk-email', [PayrollController::class, 'bulkEmail'])->name('payrolls.bulk-email');

    Route::post('/employees/bulk-assign-position', [EmployeeController::class, 'bulkAssignPosition'])->name('employees.bulk-assign-position');
    Route::post('/employees/bulk-assign-branch-site', [EmployeeController::class, 'bulkAssignBranchSite'])->name('employees.bulk-assign-branch-site');
    Route::delete('/employees/bulk-destroy', [EmployeeController::class, 'bulkDestroy']);
    Route::put('/employees/bulk-restore', [EmployeeController::class, 'bulkRestore']);
    Route::delete('/employees/bulk-force-delete', [EmployeeController::class, 'bulkForceDelete']);
    Route::put('/employees/{employee:slug_emp}/restore', [EmployeeController::class, 'restore'])->name('employees.restore')->withTrashed();
    Route::get('/payrolls/{id}/print-data', [PayrollController::class, 'getPrintData'])->name('payrolls.print-data');
    Route::get('/payrolls/export-all', [PayrollController::class, 'exportAll']); // ← before

    Route::post('/employees/bulk-assign-position', [EmployeeController::class, 'bulkAssignPosition'])->name('employees.bulk-assign-position');
    Route::post('/employees/bulk-assign-branch-site', [EmployeeController::class, 'bulkAssignBranchSite'])->name('employees.bulk-assign-branch-site');
    Route::delete('/employees/bulk-destroy', [EmployeeController::class, 'bulkDestroy']);
    Route::put('/employees/bulk-restore', [EmployeeController::class, 'bulkRestore']);
    Route::delete('/employees/bulk-force-delete', [EmployeeController::class, 'bulkForceDelete']);
    Route::put('/employees/{employee:slug_emp}/restore', [EmployeeController::class, 'restore'])->name('employees.restore')->withTrashed();

    // Payroll-specific routes — must be before resource to avoid ID conflicts
    Route::get('/payrolls/export-all', [PayrollController::class, 'exportAll'])->name('payrolls.export-all');
    Route::get('/payrolls/{id}/print-data', [PayrollController::class, 'getPrintData'])->name('payrolls.print-data');
    Route::get('/payrolls/{id}', [PayrollController::class, 'show'])->name('payrolls.show');

    // Admin dashboard
    Route::get('dashboard', AdminDashboardController::class)->name('dashboard');

    Route::get('/employee-contribution-settings', [EmployeeContributionSettingsController::class, 'getSettingsByVersion']);
    Route::get('/employees/list', [EmployeeContributionSettingsController::class, 'getEmployees']);
    Route::post('/employee-contribution-settings/bulk', [EmployeeContributionSettingsController::class, 'bulkStore']);

    Route::resource('branches', BranchController::class);
    Route::delete('/branches/{branch:branch_slug}', [BranchController::class, 'destroy'])->name('branches.destroy');
    Route::resource('positions', PositionController::class);
    Route::resource('employees', EmployeeController::class)->withTrashed();
    Route::resource('permissions', PermissionController::class);
    Route::resource('incentives', IncentiveController::class)->except(['show']);
    Route::post('/attendance/import', [AttendanceImportController::class, 'store'])->name('attendance.import');

    Route::resource('contribution-versions', ContributionVersionController::class)->except(['show']);
    Route::resource('application-leave', ApplicationLeaveController::class);
    Route::resource('payroll-periods', PayrollPeriodController::class)->except(['show', 'destroy', 'create']);

    Route::resource('payrolls', PayrollController::class)->except(['show']);

    Route::get('/attendances', [AttendanceController::class, 'attendanceManagement'])->name('attendances.index');
    Route::get('/attendance-schedules', [AttendanceController::class, 'attendanceSchedules']);
    Route::get('/attendance-period-stats', [AttendanceController::class, 'attendancePeriodStats']);
    Route::get('/attendance-logs', [AttendanceController::class, 'attendanceLogs']);
    Route::get('/attendance-exception-stats', [AttendanceController::class, 'attendanceExceptionStats']);

    Route::resource('/activity-logs', ActivityLogController::class)->only(['index']);
    Route::resource('/contributions', ContributionVersionController::class);
    Route::resource('/deductions', DeductionController::class);

    Route::resource('notifications', NotificationController::class)->only(['index', 'store', 'destroy']);
});

// ─── Employee routes ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'employee', 'auth.session', 'throttle:limit-actions', CapPerpageMiddleware::class . ':100'])->group(function () {

    Route::get('employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');

    Route::resource('employee/application-leave', EmployeeApplicationLeaveController::class)->only(['create', 'index', 'store', 'update', 'edit'])->names([
        'index'  => 'employee.application-leave.index',
        'create' => 'employee.application-leave.create',
        'store'  => 'employee.application-leave.store',
        'edit'   => 'employee.application-leave.edit',
        'update' => 'employee.application-leave.update',
    ]);
});

// ─── HR routes ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'hr', 'auth.session', 'throttle:limit-actions', CapPerpageMiddleware::class . ':100'])->group(function () {

    // HR employee bulk actions
    Route::delete('/hr/employees/bulk-destroy', [HREmployeeController::class, 'bulkDestroy']);
    Route::put('/hr/employees/bulk-restore', [HREmployeeController::class, 'bulkRestore']);
    Route::put('/hr/employees/{employee:slug_emp}/restore', [HREmployeeController::class, 'restore'])->name('hr.employees.restore');
    Route::post('/hr/employees/bulk-assign-position', [HREmployeeController::class, 'bulkAssignPosition']);
    Route::post('/hr/employees/bulk-assign-branch-site', [HREmployeeController::class, 'bulkAssignBranchSite']);

    // HR dashboard
    Route::get('hr/dashboard', HRDashboardController::class)->name('hr.dashboard');

    // HR attendance
    Route::get('/hr/attendance-logs', [HRAttendanceController::class, 'attendanceLogs'])->name('hr.attendance-logs');
    Route::get('/hr/attendance-exception-stats', [HRAttendanceController::class, 'attendanceExceptionStats'])->name('hr.attendance-exception-stats');
    Route::get('/hr/attendance-period-stats', [HRAttendanceController::class, 'attendancePeriodStats'])->name('hr.attendance-period-stats');
    Route::get('/hr/attendance-schedules', [HRAttendanceController::class, 'attendanceSchedules'])->name('hr.attendance-schedules');
    Route::get('hr/attendances', [HRAttendanceController::class, 'attendanceManagement'])->name('hr.attendances.index');
    Route::post('/hr/attendance/import', [HRAttendanceImportController::class, 'store'])->name('hr.attendance.import');


    // HR payroll email routes
    Route::post('/hr/payrolls/{payroll}/email', [HrPayrollController::class, 'emailPayroll'])->name('hr.payroll.email');
    Route::post('/hr/payrolls/bulk-email', [HrPayrollController::class, 'bulkEmail'])->name('hr.payroll.bulk-email');

    // HR contribution settings
    Route::get('/hr/employee-contribution-settings', [EmployeeContributionSettingsController::class, 'getSettingsByVersion']);
    Route::get('/hr/employees/list', [EmployeeContributionSettingsController::class, 'getEmployees']);
    Route::post('/hr/employee-contribution-settings/bulk', [EmployeeContributionSettingsController::class, 'bulkStore']);

    // HR payroll-specific routes — MUST be before resource() to avoid ID conflicts
    Route::get('/hr/payrolls/export-all', [HrPayrollController::class, 'exportAll'])->name('hr.payroll.export-all');
    Route::get('/hr/payroll/{id}/print-data', [HrPayrollController::class, 'getPrintData'])->name('hr.payroll.print-data');

    // HR payroll resource
    Route::resource('/hr/payroll', HrPayrollController::class)->names([
        'index'  => 'hr.payroll.index',
        'create' => 'hr.payroll.create',
        'store'  => 'hr.payroll.store',
        'edit'   => 'hr.payroll.edit',
        'update' => 'hr.payroll.update',
    ]);

    // HR payroll periods
    Route::resource('/hr/payroll-periods', HrPayrollPeriodController::class)->except(['show', 'create'])->names([
        'index'   => 'hr.payroll-periods.index',
        'store'   => 'hr.payroll-periods.store',
        'edit'    => 'hr.payroll-periods.edit',
        'update'  => 'hr.payroll-periods.update',
        'destroy' => 'hr.payroll-periods.destroy',
    ]);

    // HR employees
    Route::resource('hr/employees', HREmployeeController::class)->names([
        'index'   => 'hr.employees.index',
        'create'  => 'hr.employees.create',
        'store'   => 'hr.employees.store',
        'edit'    => 'hr.employees.edit',
        'show'    => 'hr.employee.show',
        'update'  => 'hr.employees.update',
        'destroy' => 'hr.employees.destroy',
    ]);

    // HR branches
    Route::resource('hr/branches', HRBranchController::class)->names([
        'index'   => 'hr.branches.index',
        'create'  => 'hr.branches.create',
        'store'   => 'hr.branches.store',
        'edit'    => 'hr.branches.edit',
        'update'  => 'hr.branches.update',
        'destroy' => 'hr.branches.destroy',
        'show'    => 'hr.branches.show',
    ]);

    // HR positions
    Route::resource('hr/positions', HRPositionController::class)->except(['show'])->names([
        'index'   => 'hr.positions.index',
        'create'  => 'hr.positions.create',
        'store'   => 'hr.positions.store',
        'edit'    => 'hr.positions.edit',
        'update'  => 'hr.positions.update',
        'destroy' => 'hr.positions.destroy',
    ]);

    // HR incentives
    Route::resource('hr/incentives', HRIncentiveController::class)->except(['show'])->names([
        'index'   => 'hr.incentives.index',
        'create'  => 'hr.incentives.create',
        'store'   => 'hr.incentives.store',
        'edit'    => 'hr.incentives.edit',
        'update'  => 'hr.incentives.update',
        'destroy' => 'hr.incentives.destroy',
    ]);

    // HR deductions
    Route::resource('hr/deductions', HRDeductionController::class)->except(['show'])->names([
        'index'   => 'hr.deductions.index',
        'create'  => 'hr.deductions.create',
        'store'   => 'hr.deductions.store',
        'edit'    => 'hr.deductions.edit',
        'update'  => 'hr.deductions.update',
        'destroy' => 'hr.deductions.destroy',
    ]);

    // HR contribution versions
    Route::resource('hr/contribution-versions', HRContributionVersionController::class)->except(['show'])->names([
        'index'   => 'hr.contribution-versions.index',
        'create'  => 'hr.contribution-versions.create',
        'store'   => 'hr.contribution-versions.store',
        'edit'    => 'hr.contribution-versions.edit',
        'update'  => 'hr.contribution-versions.update',
        'destroy' => 'hr.contribution-versions.destroy',
    ]);

    // HR application leave
    Route::resource('hr/application-leave', HRApplicationLeaveController::class)->names([
        'index'   => 'hr.application-leave.index',
        'create'  => 'hr.application-leave.create',
        'store'   => 'hr.application-leave.store',
        'edit'    => 'hr.application-leave.edit',
        'update'  => 'hr.application-leave.update',
        'destroy' => 'hr.application-leave.destroy',
        'show'    => 'hr.application-leave.show',
    ]);
});

// ─── AI routes (admin) ────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin', 'auth.session', 'throttle:limit-actions', CapPerpageMiddleware::class . ':100'])->prefix('ai')->group(function () {
    Route::get('/dashboard', [AIInsightController::class, 'dashboard']);
    Route::get('/insights', [AIInsightController::class, 'getInsights']);
    Route::post('/generate-insights', [AIInsightController::class, 'generateInsights']);
    Route::post('/deep-analysis', [AIInsightController::class, 'deepAnalysis']);
    Route::get('/attendance', [AIInsightController::class, 'analyzeAttendance']);
});

// ─── AI routes (HR) ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'hr', 'auth.session', 'throttle:limit-actions', CapPerpageMiddleware::class . ':100'])->prefix('ai/hr')->group(function () {
    Route::get('/dashboard', [HRAIInsightController::class, 'dashboard']);
    Route::get('/insights', [HRAIInsightController::class, 'getInsights']);
    Route::post('/generate-insights', [HRAIInsightController::class, 'generateInsights']);
    Route::post('/deep-analysis', [HRAIInsightController::class, 'deepAnalysis']);
    Route::get('/attendance', [HRAIInsightController::class, 'analyzeAttendance']);
});

// ─── Maintenance ──────────────────────────────────────────────────────────────
Route::get('/maintenance', function () {
    $intendedUrl = session('url.intended', '/dashboard');
    session()->forget('url.intended');

    return Inertia::render('maintenance', [
        'page'        => 'HR Dashboard',
        'message'     => 'is currently under maintenance. Please check back later.',
        'intendedUrl' => $intendedUrl,
    ]);
})->name('maintenance')->middleware('auth');


require __DIR__ . '/settings.php';
