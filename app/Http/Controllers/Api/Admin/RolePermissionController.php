<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\PermissionMatrixService;
use Exception;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    protected PermissionMatrixService $matrixService;

    public function __construct(PermissionMatrixService $matrixService)
    {
        $this->matrixService = $matrixService;
    }

    // 1. Endpoint Dashboard Super Admin: GET /api/super-admin/dashboard
    public function dashboard()
    {
        $penggunaAktif = User::where('is_active', true)->count();

        $matrix = $this->matrixService->getMatrix();
        $activePermissionsCount = 0;
        foreach ($matrix as $row) {
            foreach ($row as $key => $val) {
                if ($key !== 'label' && $val === true) {
                    $activePermissionsCount++;
                }
            }
        }

        $loginGagalHariIni = AuditLog::where('action', 'LOGIN_FAILED')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $users = User::with('employee.department')->get();
        $roleDistribution = [
            'Pegawai'       => 0,
            'Atasan'        => 0,
            'Kepala Bidang' => 0,
            'Kepala Dinas'  => 0,
            'Bendahara'     => 0,
            'Admin'         => 0,
        ];

        foreach ($users as $u) {
            $roleLabel = $this->matrixService->resolveUserRoleLabel($u);
            if ($roleLabel === 'Kabid') {
                $roleDistribution['Kepala Bidang']++;
            } elseif ($roleLabel === 'Kadis') {
                $roleDistribution['Kepala Dinas']++;
            } elseif (isset($roleDistribution[$roleLabel])) {
                $roleDistribution[$roleLabel]++;
            }
        }

        $aktivitasSistem = AuditLog::with('user.employee')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id'          => $log->id,
                    'title'       => $log->action,
                    'description' => $log->description,
                    'actor'       => $log->user->employee->name ?? 'Sistem',
                    'time_ago'    => $log->created_at ? $log->created_at->diffForHumans() : '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    'pengguna_aktif'       => $penggunaAktif,
                    'total_peran'          => 7,
                    'hak_akses_terpasang'  => $activePermissionsCount,
                    'login_gagal_hari_ini' => $loginGagalHariIni,
                    'status_sistem'        => 'Normal',
                    'status_deskripsi'     => 'semua layanan aktif',
                ],
                'pengguna_per_peran' => $roleDistribution,
                'aktivitas_sistem'   => $aktivitasSistem,
            ],
        ]);
    }

    // 2. Lihat Matriks Role & Hak Akses: GET /api/super-admin/role-permissions
    public function index()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'roles'  => ['Pegawai', 'Atasan', 'Kabid', 'Kadis', 'Bendahara', 'Admin', 'Super Admin'],
                'matrix' => $this->matrixService->getMatrix(),
            ],
        ]);
    }

    // 3. Simpan Matriks Role & Hak Akses: PUT /api/super-admin/role-permissions
    public function update(Request $request)
    {
        $validated = $request->validate([
            'matrix' => 'required|array',
        ]);

        try {
            $updatedMatrix = $this->matrixService->updateMatrix($validated['matrix']);

            AuditLog::create([
                'user_id'     => $request->user()->id,
                'action'      => 'ROLE_PERMISSION_UPDATED',
                'module'      => 'SUPER_ADMIN',
                'description' => 'Peran & hak akses diperbarui oleh Super Admin.',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Perubahan Role & Hak Akses berhasil disimpan.',
                'data'    => $updatedMatrix,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}