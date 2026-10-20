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
use App\Models\ExpenseReport;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. MASTER DEPARTEMEN
        // =========================================================================
        $deptAdmin   = Department::create(['code' => 'SUPER_ADMIN', 'name' => 'IT & System Admin']);
        $deptFinance = Department::create(['code' => 'FINANCE', 'name' => 'Divisi Keuangan']);
        $deptTravel  = Department::create(['code' => 'ADMIN_TRAVEL', 'name' => 'Logistik & Akomodasi']);
        $deptProgram = Department::create(['code' => 'PROGRAM', 'name' => 'Perencanaan & Program']);

        $defaultPassword = Hash::make('password123');

        // =========================================================================
        // 2. AKUN UTAMA (TETAP SAMA) + 2 STAF TAMBAHAN UNTUK DEMO ROMBONGAN
        // =========================================================================
        // Super Admin
        $userAdmin = User::create(['email' => 'admin@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create([
            'user_id' => $userAdmin->id, 'department_id' => $deptAdmin->id,
            'nip' => '100000001', 'name' => 'Pak Super Admin',
            'position' => 'Kepala IT', 'grade' => 'Manager'
        ]);

        // Bendahara (Finance)
        $userFinance = User::create(['email' => 'bendahara@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create([
            'user_id' => $userFinance->id, 'department_id' => $deptFinance->id,
            'nip' => '100000002', 'name' => 'Ibu Bendahara',
            'position' => 'Staff Keuangan', 'grade' => 'Staff'
        ]);

        // Admin Travel (Logistik)
        $userTravel = User::create(['email' => 'admintravel@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        Employee::create([
            'user_id' => $userTravel->id, 'department_id' => $deptTravel->id,
            'nip' => '100000003', 'name' => 'Kang Tiket',
            'position' => 'Staff Logistik', 'grade' => 'Staff'
        ]);

        // Atasan (Kabid / Manager)
        $userManager = User::create(['email' => 'manager@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        $manager = Employee::create([
            'user_id' => $userManager->id, 'department_id' => $deptProgram->id,
            'nip' => '100000004', 'name' => 'Bapak Kepala Bidang',
            'position' => 'Kepala Bidang Program', 'grade' => 'Manager'
        ]);

        // Karyawan Utama (Andi Pratama)
        $userStaff = User::create(['email' => 'karyawan@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        $staffAndi = Employee::create([
            'user_id' => $userStaff->id, 'department_id' => $deptProgram->id,
            'nip' => '100000005', 'name' => 'Andi Pratama',
            'position' => 'Staf Perencanaan', 'grade' => 'Staff',
            'direct_supervisor_id' => $manager->id
        ]);

        // Karyawan Tambahan 1 (Budi Santoso - Rekan Satu Tim Andi)
        $userBudi = User::create(['email' => 'budi@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        $staffBudi = Employee::create([
            'user_id' => $userBudi->id, 'department_id' => $deptProgram->id,
            'nip' => '100000006', 'name' => 'Budi Santoso',
            'position' => 'Analis Program', 'grade' => 'Staff',
            'direct_supervisor_id' => $manager->id
        ]);

        // Karyawan Tambahan 2 (Citra Lestari - Rekan Satu Tim Andi)
        $userCitra = User::create(['email' => 'citra@gov.trip', 'password' => $defaultPassword, 'is_active' => true]);
        $staffCitra = Employee::create([
            'user_id' => $userCitra->id, 'department_id' => $deptProgram->id,
            'nip' => '100000007', 'name' => 'Citra Lestari',
            'position' => 'Staf Evaluasi', 'grade' => 'Staff',
            'direct_supervisor_id' => $manager->id
        ]);

        // =========================================================================
        // 3. MASTER KOTA & STANDAR BIAYA MASUKAN (SBM 2026)
        // =========================================================================
        $cityJakarta    = RefCity::create(['province_name' => 'DKI Jakarta', 'city_name' => 'Jakarta Pusat']);
        $cityBandung    = RefCity::create(['province_name' => 'Jawa Barat', 'city_name' => 'Bandung']);
        $citySurabaya   = RefCity::create(['province_name' => 'Jawa Timur', 'city_name' => 'Surabaya']);
        $cityYogyakarta = RefCity::create(['province_name' => 'DI Yogyakarta', 'city_name' => 'Yogyakarta']);
        $cityDenpasar   = RefCity::create(['province_name' => 'Bali', 'city_name' => 'Denpasar']);

        $citiesForPolicy = [
            ['city' => $cityBandung,    's_allow' => 350000, 's_hotel' => 600000, 's_trans' => 200000, 'm_allow' => 500000, 'm_hotel' => 900000,  'm_trans' => 300000],
            ['city' => $citySurabaya,   's_allow' => 400000, 's_hotel' => 750000, 's_trans' => 850000, 'm_allow' => 600000, 'm_hotel' => 1200000, 'm_trans' => 1000000],
            ['city' => $cityYogyakarta, 's_allow' => 380000, 's_hotel' => 650000, 's_trans' => 600000, 'm_allow' => 550000, 'm_hotel' => 1000000, 'm_trans' => 750000],
            ['city' => $cityDenpasar,   's_allow' => 480000, 's_hotel' => 900000, 's_trans' => 1200000,'m_allow' => 700000, 'm_hotel' => 1500000, 'm_trans' => 1500000],
        ];

        foreach ($citiesForPolicy as $p) {
            TravelPolicy::create([
                'destination_city_id' => $p['city']->id, 'grade_group' => 'Staff',
                'daily_allowance' => $p['s_allow'], 'max_hotel_rate' => $p['s_hotel'],
                'max_transport_rate' => $p['s_trans'], 'effective_year' => 2026
            ]);
            TravelPolicy::create([
                'destination_city_id' => $p['city']->id, 'grade_group' => 'Manager',
                'daily_allowance' => $p['m_allow'], 'max_hotel_rate' => $p['m_hotel'],
                'max_transport_rate' => $p['m_trans'], 'effective_year' => 2026
            ]);
        }

        // =========================================================================
        // 4. DATA TRANSAKSI LENGKAP PER MODUL (UNTUK DEMO PRESENTASI FE & QA)
        // =========================================================================
        $today = Carbon::now();

        // -------------------------------------------------------------------------
        // A. MODUL KARYAWAN (TUGAS: KIRIM DRAFT, AJUKAN ULANG REVISI, SUBMIT LPJ)
        // -------------------------------------------------------------------------

        // [SKENARIO 1] Status: Draft (Karyawan bisa klik tombol "Kirim ke Atasan")
        $reqDraft = TravelRequest::create([
            'requester_employee_id' => $staffAndi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $cityYogyakarta->id,
            'start_date'            => $today->copy()->addDays(14)->format('Y-m-d'),
            'end_date'              => $today->copy()->addDays(16)->format('Y-m-d'),
            'purpose'               => 'Workshop Sinkronisasi Data Nasional 2026',
            'estimated_total_cost'  => 3040000,
            'status'                => 'Draft',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqDraft->id, 'employee_id' => $staffAndi->id, 'is_leader' => true]);

        // [SKENARIO 2] Status: Direvisi (Ditolak Atasan -> Muncul Alert Merah di FE Karyawan untuk Demo Resubmit)
        $reqRevisiAtasan = TravelRequest::create([
            'requester_employee_id' => $staffAndi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $cityDenpasar->id,
            'start_date'            => $today->copy()->addDays(10)->format('Y-m-d'),
            'end_date'              => $today->copy()->addDays(14)->format('Y-m-d'),
            'purpose'               => 'Kunjungan Kerja & Benchmarking Smart Governance',
            'estimated_total_cost'  => 7200000,
            'status'                => 'Direvisi',
            'rejection_reason'      => 'Durasi 5 hari terlalu lama untuk agenda benchmarking. Mohon dipadatkan menjadi maksimal 3 hari kerja.',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqRevisiAtasan->id, 'employee_id' => $staffAndi->id, 'is_leader' => true]);
        Approval::create([
            'travel_request_id'    => $reqRevisiAtasan->id,
            'approver_employee_id' => $manager->id,
            'status'               => 'Ditolak',
            'notes'                => 'Durasi 5 hari terlalu lama untuk agenda benchmarking. Mohon dipadatkan menjadi maksimal 3 hari kerja.',
        ]);

        // [SKENARIO 3] Status: Siap Berangkat (SPPD sudah keluar -> Karyawan siap demo "Upload LPJ")
        $reqSiapLpj = TravelRequest::create([
            'sppd_number'           => '090/SPPD-001/X/2026',
            'requester_employee_id' => $staffAndi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $cityBandung->id,
            'start_date'            => $today->copy()->subDays(3)->format('Y-m-d'),
            'end_date'              => $today->copy()->subDays(1)->format('Y-m-d'),
            'purpose'               => 'Uji Petik Infrastruktur Server Cabang Jawa Barat',
            'estimated_total_cost'  => 2450000,
            'advance_payment'       => 2450000,
            'status'                => 'Siap Berangkat',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqSiapLpj->id, 'employee_id' => $staffAndi->id, 'is_leader' => true]);
        Approval::create(['travel_request_id' => $reqSiapLpj->id, 'approver_employee_id' => $manager->id, 'status' => 'Disetujui', 'notes' => 'Disetujui, pastikan backup server berjalan.']);

        // [SKENARIO 4] Status: Revisi LPJ (Ditolak Bendahara -> Karyawan bisa demo Upload Ulang LPJ)
        $reqRevisiLpj = TravelRequest::create([
            'sppd_number'           => '090/SPPD-002/X/2026',
            'requester_employee_id' => $staffAndi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $citySurabaya->id,
            'start_date'            => $today->copy()->subDays(9)->format('Y-m-d'),
            'end_date'              => $today->copy()->subDays(7)->format('Y-m-d'),
            'purpose'               => 'Sosialisasi Modul E-Budgeting Wilayah Timur',
            'estimated_total_cost'  => 3550000,
            'advance_payment'       => 3550000,
            'status'                => 'Revisi LPJ',
            'rejection_reason'      => 'Lampiran boarding pass pesawat pulang belum terlampir dan struk hotel buram. Mohon unggah ulang.',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqRevisiLpj->id, 'employee_id' => $staffAndi->id, 'is_leader' => true]);
        ExpenseReport::create([
            'travel_request_id'  => $reqRevisiLpj->id,
            'employee_id'        => $staffAndi->id,
            'total_advance'      => 3550000,
            'total_real_expense' => 3400000,
            'balance_amount'     => 150000,
            'activity_summary'   => 'Sosialisasi diikuti oleh 45 peserta dari perwakilan cabang.',
            'status'             => 'REJECTED',
            'finance_notes'      => 'Lampiran boarding pass pesawat pulang belum terlampir dan struk hotel buram. Mohon unggah ulang.',
        ]);

        // -------------------------------------------------------------------------
        // B. MODUL ATASAN (TUGAS: 2 ANTREAN UNTUK DEMO KLIK SETUJU & KLIK TOLAK)
        // -------------------------------------------------------------------------

        // [SKENARIO 5] Antrean Approval 1 (Milik Andi + Rombongan Budi -> Untuk Demo Klik "Setuju")
        $reqWaitAtasan1 = TravelRequest::create([
            'requester_employee_id' => $staffAndi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $cityBandung->id,
            'start_date'            => $today->copy()->addDays(4)->format('Y-m-d'),
            'end_date'              => $today->copy()->addDays(6)->format('Y-m-d'),
            'purpose'               => 'Rapat Koordinasi Penyusunan RKAP 2027',
            'estimated_total_cost'  => 2450000,
            'status'                => 'Menunggu Persetujuan Atasan',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqWaitAtasan1->id, 'employee_id' => $staffAndi->id, 'is_leader' => true]);
        TravelAssignee::create(['travel_request_id' => $reqWaitAtasan1->id, 'employee_id' => $staffBudi->id, 'is_leader' => false]);

        // [SKENARIO 6] Antrean Approval 2 (Milik Budi -> Untuk Demo Klik "Tolak/Revisi" di depan PM)
        $reqWaitAtasan2 = TravelRequest::create([
            'requester_employee_id' => $staffBudi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $citySurabaya->id,
            'start_date'            => $today->copy()->addDays(6)->format('Y-m-d'),
            'end_date'              => $today->copy()->addDays(8)->format('Y-m-d'),
            'purpose'               => 'Inspeksi Mendadak Gudang Logistik Regional',
            'estimated_total_cost'  => 3550000,
            'status'                => 'Menunggu Persetujuan Atasan',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqWaitAtasan2->id, 'employee_id' => $staffBudi->id, 'is_leader' => true]);

        // -------------------------------------------------------------------------
        // C. MODUL ADMIN TRAVEL (TUGAS: 2 ANTREAN UNTUK GENERATE SPPD & BATALKAN)
        // -------------------------------------------------------------------------

        // [SKENARIO 7] Antrean Logistik 1 (Untuk Demo Klik "Generate SPPD & Input Tiket")
        $reqWaitTravel1 = TravelRequest::create([
            'requester_employee_id' => $staffAndi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $citySurabaya->id,
            'start_date'            => $today->copy()->addDays(5)->format('Y-m-d'),
            'end_date'              => $today->copy()->addDays(7)->format('Y-m-d'),
            'purpose'               => 'Survei Kesiapan Jaringan Fiber Optik Cabang',
            'estimated_total_cost'  => 3550000,
            'status'                => 'Disetujui',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqWaitTravel1->id, 'employee_id' => $staffAndi->id, 'is_leader' => true]);
        Approval::create(['travel_request_id' => $reqWaitTravel1->id, 'approver_employee_id' => $manager->id, 'status' => 'Disetujui', 'notes' => 'Segera proses tiket keberangkatan.']);

        // [SKENARIO 8] Antrean Logistik 2 (Untuk Demo Klik "Reject/Batalkan" karena kuota tiket habis)
        $reqWaitTravel2 = TravelRequest::create([
            'requester_employee_id' => $staffCitra->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $cityYogyakarta->id,
            'start_date'            => $today->copy()->addDays(8)->format('Y-m-d'),
            'end_date'              => $today->copy()->addDays(10)->format('Y-m-d'),
            'purpose'               => 'Pendampingan Audit Internal Triwulan III',
            'estimated_total_cost'  => 3040000,
            'status'                => 'Disetujui',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqWaitTravel2->id, 'employee_id' => $staffCitra->id, 'is_leader' => true]);
        Approval::create(['travel_request_id' => $reqWaitTravel2->id, 'approver_employee_id' => $manager->id, 'status' => 'Disetujui', 'notes' => 'ACC, koordinasikan dengan tim audit.']);

        // -------------------------------------------------------------------------
        // D. MODUL FINANCE / BENDAHARA (TUGAS: 2 ANTREAN VERIFIKASI LPJ & CETAK PDF)
        // -------------------------------------------------------------------------

        // [SKENARIO 9] Antrean Verifikasi LPJ 1 (Untuk Demo Klik "Verifikasi / Setuju")
        $reqWaitFinance1 = TravelRequest::create([
            'sppd_number'           => '090/SPPD-003/X/2026',
            'requester_employee_id' => $staffAndi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $cityBandung->id,
            'start_date'            => $today->copy()->subDays(12)->format('Y-m-d'),
            'end_date'              => $today->copy()->subDays(10)->format('Y-m-d'),
            'purpose'               => 'Integrasi API Pembayaran Pajak Daerah',
            'estimated_total_cost'  => 2450000,
            'advance_payment'       => 2450000,
            'status'                => 'Menunggu Verifikasi LPJ',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqWaitFinance1->id, 'employee_id' => $staffAndi->id, 'is_leader' => true]);
        ExpenseReport::create([
            'travel_request_id'  => $reqWaitFinance1->id,
            'employee_id'        => $staffAndi->id,
            'total_advance'      => 2450000,
            'total_real_expense' => 2300000,
            'balance_amount'     => 150000,
            'activity_summary'   => 'Integrasi API berhasil diselesaikan tepat waktu dan sudah diuji di server staging.',
            'status'             => 'SUBMITTED',
        ]);

        // [SKENARIO 10] Antrean Verifikasi LPJ 2 (Untuk Demo Klik "Tolak LPJ / Minta Revisi")
        $reqWaitFinance2 = TravelRequest::create([
            'sppd_number'           => '090/SPPD-004/X/2026',
            'requester_employee_id' => $staffBudi->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $cityYogyakarta->id,
            'start_date'            => $today->copy()->subDays(15)->format('Y-m-d'),
            'end_date'              => $today->copy()->subDays(13)->format('Y-m-d'),
            'purpose'               => 'Pelatihan Keamanan Siber & Manajemen Insiden',
            'estimated_total_cost'  => 3040000,
            'advance_payment'       => 3040000,
            'status'                => 'Menunggu Verifikasi LPJ',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqWaitFinance2->id, 'employee_id' => $staffBudi->id, 'is_leader' => true]);
        ExpenseReport::create([
            'travel_request_id'  => $reqWaitFinance2->id,
            'employee_id'        => $staffBudi->id,
            'total_advance'      => 3040000,
            'total_real_expense' => 3040000,
            'balance_amount'     => 0,
            'activity_summary'   => 'Mengikuti seluruh rangkaian materi keamanan siber dan mendapatkan sertifikat kelulusan.',
            'status'             => 'SUBMITTED',
        ]);

        // -------------------------------------------------------------------------
        // E. DATA SELESAI (MENGHIDUPKAN GRAFIK, TOTAL REALISASI, & CETAK PDF FINAL)
        // -------------------------------------------------------------------------

        // [SKENARIO 11] Status: Selesai (Rentang tanggal mencakup HARI INI agar "Sedang Dinas" di Dashboard Atasan menyala & Finance bisa langsung Demo Cetak PDF!)
        $reqSelesai1 = TravelRequest::create([
            'sppd_number'           => '090/SPPD-005/X/2026',
            'requester_employee_id' => $staffCitra->id,
            'origin_city_id'        => $cityJakarta->id,
            'destination_city_id'   => $cityBandung->id,
            'start_date'            => $today->copy()->subDays(1)->format('Y-m-d'),
            'end_date'              => $today->copy()->addDays(1)->format('Y-m-d'),
            'purpose'               => 'Evaluasi Kinerja Semester II & Audit Lapangan',
            'estimated_total_cost'  => 2450000,
            'advance_payment'       => 2450000,
            'status'                => 'Selesai',
        ]);
        TravelAssignee::create(['travel_request_id' => $reqSelesai1->id, 'employee_id' => $staffCitra->id, 'is_leader' => true]);
        Approval::create(['travel_request_id' => $reqSelesai1->id, 'approver_employee_id' => $manager->id, 'status' => 'Disetujui', 'notes' => 'Laksanakan sesuai jadwal.']);
        ExpenseReport::create([
            'travel_request_id'  => $reqSelesai1->id,
            'employee_id'        => $staffCitra->id,
            'total_advance'      => 2450000,
            'total_real_expense' => 2250000,
            'balance_amount'     => 200000,
            'activity_summary'   => 'Audit lapangan selesai 100%, selisih anggaran Rp 200.000 telah disetor kembali ke kas negara/perusahaan.',
            'status'             => 'VERIFIED',
            'finance_notes'      => 'Bukti kuitansi lengkap dan setoran sisa uang muka telah diterima.',
        ]);
    }
}