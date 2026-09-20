<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartment
{
    public function handle(Request $request, Closure $next, ...$allowedDepartments): Response
    {
        $user = $request->user();

        // 1. Pastikan user memiliki relasi employee dan department
        if (!$user || !$user->employee || !$user->employee->department) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Profil pegawai atau departemen tidak ditemukan.'
            ], 403);
        }

        // 2. Ambil kode departemen dan jadikan UPPERCASE agar aman dari typo (misal: 'finance' jadi 'FINANCE')
        $userDeptCode = strtoupper($user->employee->department->code);

        // 3. [Revisi God Mode] Beri akses penuh jika user adalah IT atau SUPER_ADMIN
        if ($userDeptCode === 'IT' || $userDeptCode === 'SUPER_ADMIN') {
            return $next($request);
        }

        // 4. Ubah allowedDepartments menjadi uppercase semua untuk pencocokan yang akurat
        $allowedDepartments = array_map('strtoupper', $allowedDepartments);

        // 5. Cek apakah kode departemen user ada di dalam daftar yang diizinkan route
        if (!in_array($userDeptCode, $allowedDepartments)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki wewenang untuk mengakses modul ini.'
            ], 403);
        }

        return $next($request);
    }
}