<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TravelRequestController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\TravelLogisticsController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\NotificationController;

// Super Admin Routes
use App\Http\Controllers\Api\Admin\DepartmentController;
use App\Http\Controllers\Api\Admin\EmployeeController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\TravelPolicyController;
use App\Http\Controllers\Api\Admin\AuditLogController;

Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Wajib Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead']);
    // --- MODUL KARYAWAN (PEMOHON) ---
    Route::prefix('travel-requests')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [TravelRequestController::class, 'index']);      // List Riwayat Saya
        Route::get('/dashboard', [TravelRequestController::class, 'dashboard']); // Dashboard Ringkas
        Route::get('/{id}', [TravelRequestController::class, 'show']);   // Detail / Tracking
        Route::post('/', [TravelRequestController::class, 'store']);     // Buat Pengajuan Baru

        // Karyawan lapor pulang (yang sudah kita bahas sebelumnya)
        Route::post('/{id}/report', [TravelRequestController::class, 'submitReport']);
    });
    // MODUL APPROVAL (Atasan, Kabid, Kadis)
    Route::prefix('approvals')->middleware('dept:PROGRAM')->group(function () {
        Route::get('/', [ApprovalController::class, 'index']);      // List Card
        Route::get('/dashboard', [ApprovalController::class, 'dashboard']); // Dashboard Ringkas`
        Route::get('/{id}', [ApprovalController::class, 'show']);   // Detail Drawer
        Route::get('/history', [ApprovalController::class, 'history']); // List Riwayat Saya (Atasan)
        Route::post('/{id}', [ApprovalController::class, 'decide']); // Action Setuju/Tolak (yang sudah jalan)
    });

    // --- MODUL ADMIN TRAVEL ---
    Route::prefix('logistics')->middleware('dept:ADMIN_TRAVEL')->group(function () {
        Route::get('/dashboard', [TravelLogisticsController::class, 'dashboard']); // Dashboard Ringkas
        Route::get('/', [TravelLogisticsController::class, 'index']);      // List Card
        Route::get('/{id}', [TravelLogisticsController::class, 'show']);   // Detail
        Route::post('/{id}', [TravelLogisticsController::class, 'store']); // Aksi Input Tiket
        Route::post('/{id}/generate-sppd', [TravelLogisticsController::class, 'generateSppd']);
    });

    // --- MODUL FINANCE ---
    Route::prefix('finance')->middleware('dept:FINANCE')->group(function () {
        Route::get('/verifications', [FinanceController::class, 'index']);      // List Card
        Route::get('/verifications/dashboard', [FinanceController::class, 'dashboard']); // Dashboard Ringkas
        Route::get('/verifications/history', [FinanceController::class, 'history']); //
        Route::get('/verifications/{id}', [FinanceController::class, 'show']);  // Detail
        Route::post('/verifications/{id}', [FinanceController::class, 'verify']); // Aksi ACC Dana

        // Route khusus cetak PDF (tetap dipertahankan)
        Route::get('/verifications/{id}/lpj/download', [FinanceController::class, 'downloadFinalAuditReport']);
    });

    // MODUL SUPER ADMIN (Master Data & Akun)
    Route::prefix('super-admin')->middleware('dept:SUPER_ADMIN')->group(function () {

        // --- CRUD DEPARTMENTS ---
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::get('/departments/{id}', [DepartmentController::class, 'show']);
        Route::put('/departments/{id}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

        // --- CRUD EMPLOYEES & USERS ---
        Route::get('/employees', [EmployeeController::class, 'index']);
        Route::post('/employees', [EmployeeController::class, 'store']);
        Route::get('/employees/{id}', [EmployeeController::class, 'show']);
        Route::put('/employees/{id}', [EmployeeController::class, 'update']);
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

        // --- CRUD CITIES ---
        Route::get('/cities', [CityController::class, 'index']);
        Route::post('/cities', [CityController::class, 'store']);
        Route::get('/cities/{id}', [CityController::class, 'show']);
        Route::put('/cities/{id}', [CityController::class, 'update']);
        Route::delete('/cities/{id}', [CityController::class, 'destroy']);

        // --- CRUD TRAVEL POLICIES ---
        Route::get('/policies', [TravelPolicyController::class, 'index']);
        Route::post('/policies', [TravelPolicyController::class, 'store']);
        Route::get('/policies/{id}', [TravelPolicyController::class, 'show']);
        Route::put('/policies/{id}', [TravelPolicyController::class, 'update']);
        Route::delete('/policies/{id}', [TravelPolicyController::class, 'destroy']);

        // --- AUDIT LOGS ---
        Route::get('/audit-logs', [AuditLogController::class, 'index']); // Read-Only
    });
});