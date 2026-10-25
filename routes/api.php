<?php

use App\Http\Controllers\Api\Admin\AuditLogController;
use App\Http\Controllers\Api\Admin\RolePermissionController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\DepartmentController;
use App\Http\Controllers\Api\Admin\EmployeeController;
use App\Http\Controllers\Api\Admin\TravelPolicyController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TravelLogisticsController;
use App\Http\Controllers\Api\TravelRequestController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Wajib Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead']);
    // --- MODUL KARYAWAN (PEMOHON) ---
    Route::prefix('travel-requests')->middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', [TravelRequestController::class, 'dashboard']);
        Route::get('/form-options', [TravelRequestController::class, 'formOptions']);
        Route::get('/', [TravelRequestController::class, 'index']);
        Route::get('/{id}', [TravelRequestController::class, 'show']);

        // Preview JSON sebelum Download PDF + Endpoint Download PDF
        Route::get('/{id}/preview', [TravelRequestController::class, 'previewDocument']);
        Route::get('/{id}/sppd', [TravelRequestController::class, 'downloadSppd']);
        Route::get('/{id}/lpj-final', [TravelRequestController::class, 'downloadLpjFinal']);

        // Aksi Buat, Edit Revisi, & Kirim ke Atasan
        Route::post('/', [TravelRequestController::class, 'store']);
        Route::put('/{id}', [TravelRequestController::class, 'update']);
        Route::post('/{id}/submit', [TravelRequestController::class, 'submit']);
    });
    // MODUL APPROVAL (Atasan, Kabid, Kadis)
    Route::prefix('approvals')->middleware('dept:PROGRAM')->group(function () {
        Route::get('/', [ApprovalController::class, 'index']);              // List Card
        Route::get('/dashboard', [ApprovalController::class, 'dashboard']); // Dashboard Ringkas (Wajib di atas /{id})
        Route::get('/history', [ApprovalController::class, 'history']);     // List Riwayat (Wajib di atas /{id})
        Route::get('/{id}', [ApprovalController::class, 'show']);           // Detail Drawer

        // Action Setuju / Tolak / Revisi (Mendukung rute lama maupun rute spesifik)
        Route::post('/{id}', [ApprovalController::class, 'decide']);
        Route::post('/{id}/approve', [ApprovalController::class, 'approve']);
        Route::post('/{id}/reject', [ApprovalController::class, 'reject']);
    });

    // --- MODUL ADMIN TRAVEL ---
    Route::prefix('logistics')->middleware('dept:ADMIN_TRAVEL')->group(function () {
        Route::get('/dashboard', [TravelLogisticsController::class, 'dashboard']);     // Dashboard Ringkas
        Route::get('/master-data', [TravelLogisticsController::class, 'masterData']); // Referensi Kota, SBM, & Transportasi
        Route::get('/', [TravelLogisticsController::class, 'index']);                  // List Card Antrean
        Route::get('/{id}', [TravelLogisticsController::class, 'show']);               // Detail + Rincian Plafon SBM Otomatis
        Route::post('/{id}', [TravelLogisticsController::class, 'store']);             // Aksi Input Tiket & Kunci Budget SBM
        Route::post('/{id}/reject', [TravelLogisticsController::class, 'reject']);     // Aksi Tolak / Kembalikan Logistik
    });

    // --- MODUL FINANCE ---
    Route::prefix('finance')->middleware('dept:FINANCE')->group(function () {
        Route::get('/verifications', [FinanceController::class, 'index']);                  // List Card Antrean
        Route::get('/verifications/dashboard', [FinanceController::class, 'dashboard']);    // Dashboard Ringkas
        Route::get('/verifications/history', [FinanceController::class, 'history']);        // Riwayat Verifikasi
        Route::get('/verifications/{id}', [FinanceController::class, 'show']);              // Detail Stopmap
        Route::post('/verifications/{id}', [FinanceController::class, 'verify']);           // Aksi ACC / Tolak Dana
        Route::post('/verifications/{id}/reject', [FinanceController::class, 'reject']);    // Aksi Khusus Form Reject

        // Route khusus cetak PDF
        Route::get('/verifications/{id}/lpj/download', [FinanceController::class, 'downloadFinalAuditReport']);
    });

    // MODUL SUPER ADMIN (Master Data & Akun)
    Route::prefix('super-admin')->middleware('dept:SUPER_ADMIN')->group(function () {

        // --- DASHBOARD & MATRIKS ROLE HAK AKSES ---
        Route::get('/dashboard', [RolePermissionController::class, 'dashboard']);
        Route::get('/role-permissions', [RolePermissionController::class, 'index']);
        Route::put('/role-permissions', [RolePermissionController::class, 'update']);

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
        Route::patch('/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus']); // Saklar Aktif/Nonaktif UI
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
