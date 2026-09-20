<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;
use App\Models\User;
use App\Models\Employee;
use App\Models\RefCity;
use App\Models\TravelPolicy;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT MASTER DEPARTEMEN
        $deptAdmin   = Department::create(['code' => 'SUPER_ADMIN', 'name' => 'IT & System Admin']);
        $deptFinance = Department::create(['code' => 'FINANCE', 'name' => 'Divisi Keuangan']);
        $deptTravel  = Department::create(['code' => 'ADMIN_TRAVEL', 'name' => 'Logistik & Akomodasi']);
        $deptProgram = Department::create(['code' => 'PROGRAM', 'name' => 'Perencanaan & Program']);

        $defaultPassword = Hash::make('password123');

        // 2. BUAT AKUN SUPER ADMIN
        $userAdmin = User::create(['email' => 'admin@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create([
            'user_id' => $userAdmin->id, 'department_id' => $deptAdmin->id, 
            'nip' => '100000001', 'name' => 'Pak Super Admin', 
            'position' => 'Kepala IT', 'grade' => 'Manager'
        ]);

        // 3. BUAT AKUN BENDAHARA (FINANCE)
        $userFinance = User::create(['email' => 'bendahara@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create([
            'user_id' => $userFinance->id, 'department_id' => $deptFinance->id, 
            'nip' => '100000002', 'name' => 'Ibu Bendahara', 
            'position' => 'Staff Keuangan', 'grade' => 'Staff'
        ]);

        // 4. BUAT AKUN ADMIN TRAVEL
        $userTravel = User::create(['email' => 'admintravel@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create([
            'user_id' => $userTravel->id, 'department_id' => $deptTravel->id, 
            'nip' => '100000003', 'name' => 'Kang Tiket', 
            'position' => 'Staff Logistik', 'grade' => 'Staff'
        ]);

        // 5. BUAT AKUN ATASAN (KADIS/KABID)
        $userManager = User::create(['email' => 'manager@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        $manager = Employee::create([
            'user_id' => $userManager->id, 'department_id' => $deptProgram->id, 
            'nip' => '100000004', 'name' => 'Bapak Kepala Bidang', 
            'position' => 'Kepala Bidang Program', 'grade' => 'Manager'
        ]);

        // 6. BUAT AKUN KARYAWAN (Bawahan langsung dari Manager)
        $userStaff = User::create(['email' => 'karyawan@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create([
            'user_id' => $userStaff->id, 'department_id' => $deptProgram->id, 
            'nip' => '100000005', 'name' => 'Andi Pratama', 
            'position' => 'Staf Perencanaan', 'grade' => 'Staff',
            'direct_supervisor_id' => $manager->id // Kunci Relasi Approval!
        ]);

        // 7. BUAT DATA KOTA
        $cityJakarta = RefCity::create(['province_name' => 'DKI Jakarta', 'city_name' => 'Jakarta Pusat']);
        $cityBandung = RefCity::create(['province_name' => 'Jawa Barat', 'city_name' => 'Bandung']);
        $citySurabaya = RefCity::create(['province_name' => 'Jawa Timur', 'city_name' => 'Surabaya']);

        // 8. BUAT SBM (STANDAR BIAYA) TAHUN 2026
        TravelPolicy::create([
            'destination_city_id' => $cityBandung->id, 'grade_group' => 'Staff',
            'daily_allowance' => 350000, 'max_hotel_rate' => 600000, 
            'max_transport_rate' => 200000, 'effective_year' => 2026
        ]);
        TravelPolicy::create([
            'destination_city_id' => $cityBandung->id, 'grade_group' => 'Manager',
            'daily_allowance' => 500000, 'max_hotel_rate' => 900000, 
            'max_transport_rate' => 300000, 'effective_year' => 2026
        ]);
    }
}