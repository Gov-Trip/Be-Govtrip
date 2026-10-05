<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;
use App\Models\User;
use App\Models\Employee;
use App\Models\RefCity;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\TravelAssignee;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\ExpenseReport;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. DATA MASTER & PENGGUNA
        // ==========================================
        $deptAdmin   = Department::create(['code' => 'SUPER_ADMIN', 'name' => 'IT & System Admin']);
        $deptFinance = Department::create(['code' => 'FINANCE', 'name' => 'Divisi Keuangan']);
        $deptTravel  = Department::create(['code' => 'ADMIN_TRAVEL', 'name' => 'Logistik & Akomodasi']);
        $deptProgram = Department::create(['code' => 'PROGRAM', 'name' => 'Perencanaan & Program']);

        $defaultPassword = Hash::make('password123');

        $userAdmin = User::create(['email' => 'admin@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create(['user_id' => $userAdmin->id, 'department_id' => $deptAdmin->id, 'nip' => '100000001', 'name' => 'Pak Super Admin', 'position' => 'Kepala IT', 'grade' => 'Manager']);

        $userFinance = User::create(['email' => 'bendahara@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        $empFinance = Employee::create(['user_id' => $userFinance->id, 'department_id' => $deptFinance->id, 'nip' => '100000002', 'name' => 'Ibu Bendahara', 'position' => 'Staff Keuangan', 'grade' => 'Staff']);

        $userTravel = User::create(['email' => 'admintravel@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create(['user_id' => $userTravel->id, 'department_id' => $deptTravel->id, 'nip' => '100000003', 'name' => 'Kang Tiket', 'position' => 'Staff Logistik', 'grade' => 'Staff']);

        $userManager = User::create(['email' => 'manager@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        $manager = Employee::create(['user_id' => $userManager->id, 'department_id' => $deptProgram->id, 'nip' => '100000004', 'name' => 'Bapak Kepala Bidang', 'position' => 'Kepala Bidang Program', 'grade' => 'Manager']);

        $userStaff = User::create(['email' => 'karyawan@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        $staff = Employee::create(['user_id' => $userStaff->id, 'department_id' => $deptProgram->id, 'nip' => '100000005', 'name' => 'Andi Pratama', 'position' => 'Staf Perencanaan', 'grade' => 'Staff', 'direct_supervisor_id' => $manager->id]);

        $cityJakarta = RefCity::create(['province_name' => 'DKI Jakarta', 'city_name' => 'Jakarta Pusat']);
        $cityBandung = RefCity::create(['province_name' => 'Jawa Barat', 'city_name' => 'Bandung']);
        $citySurabaya = RefCity::create(['province_name' => 'Jawa Timur', 'city_name' => 'Surabaya']);

        TravelPolicy::create(['destination_city_id' => $cityBandung->id, 'grade_group' => 'Staff', 'daily_allowance' => 350000, 'max_hotel_rate' => 600000, 'max_transport_rate' => 200000, 'effective_year' => 2026]);
        TravelPolicy::create(['destination_city_id' => $cityBandung->id, 'grade_group' => 'Manager', 'daily_allowance' => 500000, 'max_hotel_rate' => 900000, 'max_transport_rate' => 300000, 'effective_year' => 2026]);


        // ==========================================
        // 2. DATA DUMMY TRANSAKSI (UNTUK QA & FE)
        // ==========================================
        $today = Carbon::now();

        // SCENARIO 1: Menunggu Persetujuan Atasan (Baru dibuat oleh Karyawan)
        $req1 = TravelRequest::create([
            'requester_employee_id' => $staff->id,
            'origin_city_id' => $cityJakarta->id,
            'destination_city_id' => $cityBandung->id,
            'start_date' => $today->copy()->addDays(3)->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(5)->format('Y-m-d'),
            'purpose' => 'Rapat Koordinasi Perencanaan Q4',
            'estimated_total_cost' => 1500000,
            'status' => 'Menunggu Persetujuan Atasan'
        ]);
        TravelAssignee::create(['travel_request_id' => $req1->id, 'employee_id' => $staff->id, 'is_leader' => true]);

        // SCENARIO 2: Disetujui Atasan, Menunggu Admin Travel Terbitkan SPPD & Tiket
        $req2 = TravelRequest::create([
            'requester_employee_id' => $staff->id,
            'origin_city_id' => $cityJakarta->id,
            'destination_city_id' => $citySurabaya->id,
            'start_date' => $today->copy()->addDays(7)->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(9)->format('Y-m-d'),
            'purpose' => 'Survei Lokasi Proyek Baru',
            'estimated_total_cost' => 4500000,
            'status' => 'Disetujui'
        ]);
        TravelAssignee::create(['travel_request_id' => $req2->id, 'employee_id' => $staff->id, 'is_leader' => true]);
        Approval::create(['travel_request_id' => $req2->id, 'approver_employee_id' => $manager->id, 'status' => 'Disetujui', 'notes' => 'Silakan berangkat, jaga kesehatan.']);

        // SCENARIO 3: SPPD & Uang Muka Sudah Cair (Siap Berangkat / Sedang Dinas)
        $req3 = TravelRequest::create([
            'sppd_number' => 'SPPD/2026/09/X001',
            'requester_employee_id' => $manager->id, // Yang berangkat Manajernya
            'origin_city_id' => $cityJakarta->id,
            'destination_city_id' => $cityBandung->id,
            'start_date' => $today->copy()->subDays(1)->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(1)->format('Y-m-d'),
            'purpose' => 'Monitoring Evaluasi Cabang Bandung',
            'estimated_total_cost' => 2000000,
            'advance_payment' => 2000000,
            'status' => 'Siap Berangkat'
        ]);
        TravelAssignee::create(['travel_request_id' => $req3->id, 'employee_id' => $manager->id, 'is_leader' => true]);

        // SCENARIO 4: Sudah Pulang & Kumpul LPJ (Menunggu Verifikasi Bendahara)
        $req4 = TravelRequest::create([
            'sppd_number' => 'SPPD/2026/08/X002',
            'requester_employee_id' => $staff->id,
            'origin_city_id' => $cityJakarta->id,
            'destination_city_id' => $cityBandung->id,
            'start_date' => $today->copy()->subDays(10)->format('Y-m-d'),
            'end_date' => $today->copy()->subDays(8)->format('Y-m-d'),
            'purpose' => 'Training Penggunaan Alat Baru',
            'estimated_total_cost' => 1500000,
            'advance_payment' => 1500000,
            'status' => 'Menunggu Verifikasi LPJ'
        ]);
        TravelAssignee::create(['travel_request_id' => $req4->id, 'employee_id' => $staff->id, 'is_leader' => true]);
        ExpenseReport::create([
            'travel_request_id' => $req4->id,
            'employee_id' => $staff->id,
            'total_advance' => 1500000,
            'total_real_expense' => 1400000, // Sisa uang 100rb
            'balance_amount' => 100000,
            'activity_summary' => 'Training berjalan lancar, alat sudah dikuasai.',
            'status' => 'SUBMITTED'
        ]);
    }
}