<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PermissionMatrixService
{
    protected string $filePath = 'role_permissions.json';

    /**
     * Daftar 7 peran standar sesuai kolom pada UI Role & Hak Akses.
     */
    public function getRoles(): array
    {
        return [
            'Pegawai',
            'Atasan',
            'Kabid',
            'Kadis',
            'Bendahara',
            'Admin',
            'Super Admin',
        ];
    }

    /**
     * Matriks default sesuai kondisi awal pada UI Role & Hak Akses.
     */
    public function getDefaultMatrix(): array
    {
        return [
            'ajukan_perjalanan' => [
                'label'       => 'Ajukan perjalanan dinas',
                'Pegawai'     => true,
                'Atasan'      => false,
                'Kabid'       => false,
                'Kadis'       => false,
                'Bendahara'   => false,
                'Admin'       => false,
                'Super Admin' => false,
            ],
            'setujui_pengajuan' => [
                'label'       => 'Setujui pengajuan',
                'Pegawai'     => false,
                'Atasan'      => true,
                'Kabid'       => true,
                'Kadis'       => true,
                'Bendahara'   => false,
                'Admin'       => false,
                'Super Admin' => false,
            ],
            'verifikasi_laporan' => [
                'label'       => 'Verifikasi laporan',
                'Pegawai'     => false,
                'Atasan'      => false,
                'Kabid'       => false,
                'Kadis'       => false,
                'Bendahara'   => true,
                'Admin'       => false,
                'Super Admin' => false,
            ],
            'kelola_sppd_digital' => [
                'label'       => 'Kelola SPPD digital',
                'Pegawai'     => false,
                'Atasan'      => false,
                'Kabid'       => false,
                'Kadis'       => false,
                'Bendahara'   => false,
                'Admin'       => true,
                'Super Admin' => false,
            ],
            'kelola_data_pegawai' => [
                'label'       => 'Kelola data pegawai & perjalanan',
                'Pegawai'     => false,
                'Atasan'      => false,
                'Kabid'       => false,
                'Kadis'       => false,
                'Bendahara'   => false,
                'Admin'       => true,
                'Super Admin' => true,
            ],
            'monitoring_perjalanan' => [
                'label'       => 'Monitoring perjalanan',
                'Pegawai'     => false,
                'Atasan'      => true,
                'Kabid'       => true,
                'Kadis'       => true,
                'Bendahara'   => true,
                'Admin'       => true,
                'Super Admin' => true,
            ],
            'kelola_pengguna_peran' => [
                'label'       => 'Kelola pengguna & peran',
                'Pegawai'     => false,
                'Atasan'      => false,
                'Kabid'       => false,
                'Kadis'       => false,
                'Bendahara'   => false,
                'Admin'       => false,
                'Super Admin' => true,
            ],
            'konfigurasi_sistem' => [
                'label'       => 'Konfigurasi sistem',
                'Pegawai'     => false,
                'Atasan'      => false,
                'Kabid'       => false,
                'Kadis'       => false,
                'Bendahara'   => false,
                'Admin'       => true,
                'Super Admin' => true,
            ],
        ];
    }

    /**
     * Ambil matriks hak akses saat ini dari storage lokal.
     */
    public function getMatrix(): array
    {
        if (! Storage::disk('local')->exists($this->filePath)) {
            $default = $this->getDefaultMatrix();
            Storage::disk('local')->put($this->filePath, json_encode($default, JSON_PRETTY_PRINT));

            return $default;
        }

        $content = Storage::disk('local')->get($this->filePath);
        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            return $this->resetToDefault();
        }

        return array_replace_recursive($this->getDefaultMatrix(), $decoded);
    }

    /**
     * Simpan perubahan matriks dari tombol "Simpan perubahan" Super Admin.
     */
    public function updateMatrix(array $newMatrix): array
    {
        $current = $this->getMatrix();
        $validRoles = $this->getRoles();

        foreach ($newMatrix as $permissionKey => $roles) {
            if (! isset($current[$permissionKey]) || ! is_array($roles)) {
                continue;
            }

            foreach ($roles as $roleName => $isAllowed) {
                if (in_array($roleName, $validRoles, true)) {
                    $current[$permissionKey][$roleName] = filter_var($isAllowed, FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        Storage::disk('local')->put($this->filePath, json_encode($current, JSON_PRETTY_PRINT));

        return $current;
    }

    /**
     * Kembalikan matriks ke pengaturan awal (berguna setelah demo/testing QA).
     */
    public function resetToDefault(): array
    {
        $default = $this->getDefaultMatrix();
        Storage::disk('local')->put($this->filePath, json_encode($default, JSON_PRETTY_PRINT));

        return $default;
    }

    /**
     * Petakan User yang sedang login ke salah satu dari 7 nama peran.
     */
    public function resolveUserRoleLabel(User $user): string
    {
        $user->loadMissing('employee.department');
        $employee = $user->employee;

        if (! $employee) {
            return 'Pegawai';
        }

        $deptCode = strtoupper($employee->department->code ?? '');
        $position = strtolower($employee->position ?? '');

        if ($deptCode === 'SUPER_ADMIN') {
            return 'Super Admin';
        }

        if ($deptCode === 'FINANCE') {
            return 'Bendahara';
        }

        if ($deptCode === 'ADMIN_TRAVEL') {
            return 'Admin';
        }

        if (str_contains($position, 'kepala dinas') || str_contains($position, 'kadis')) {
            return 'Kadis';
        }

        if (str_contains($position, 'kepala bidang') || str_contains($position, 'kabid')) {
            return 'Kabid';
        }

        if ($employee->grade === 'Manager' || str_contains($position, 'atasan')) {
            return 'Atasan';
        }

        return 'Pegawai';
    }

    /**
     * Cek apakah user yang login memiliki hak akses tertentu pada matriks aktif.
     */
    public function hasPermission(User $user, string $permissionKey): bool
    {
        $roleLabel = $this->resolveUserRoleLabel($user);
        $matrix = $this->getMatrix();

        return ! empty($matrix[$permissionKey][$roleLabel]);
    }

    /**
     * Ambil daftar lengkap boolean hak akses milik user yang sedang login (untuk keperluan UI Frontend).
     */
    public function getUserPermissions(User $user): array
    {
        $roleLabel = $this->resolveUserRoleLabel($user);
        $matrix = $this->getMatrix();
        $permissions = [];

        foreach ($matrix as $key => $row) {
            $permissions[$key] = (bool) ($row[$roleLabel] ?? false);
        }

        return [
            'role'        => $roleLabel,
            'permissions' => $permissions,
        ];
    }
}