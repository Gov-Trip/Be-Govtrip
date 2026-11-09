<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\RefCity;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use App\Services\PermissionMatrixService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GovtripWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $manager;

    protected User $pegawai;

    protected User $adminTravel;

    protected User $bendahara;

    protected RefCity $cityOrigin;

    protected RefCity $cityDest;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Departments
        $deptAdmin = Department::create(['code' => 'SUPER_ADMIN', 'name' => 'IT & System Admin']);
        $deptFinance = Department::create(['code' => 'FINANCE', 'name' => 'Divisi Keuangan']);
        $deptTravel = Department::create(['code' => 'ADMIN_TRAVEL', 'name' => 'Logistik & Akomodasi']);
        $deptProgram = Department::create(['code' => 'PROGRAM', 'name' => 'Perencanaan & Program']);

        $pwd = Hash::make('password123');

        // 2. Setup Users & Employees
        $this->superAdmin = User::create(['email' => 'admin@gov.trip', 'password' => $pwd, 'is_active' => true]);
        Employee::create([
            'user_id' => $this->superAdmin->id, 'department_id' => $deptAdmin->id,
            'nip' => '100000001', 'name' => 'Pak Super Admin', 'position' => 'Kepala IT', 'grade' => 'Manager',
        ]);

        $this->bendahara = User::create(['email' => 'bendahara@gov.trip', 'password' => $pwd, 'is_active' => true]);
        Employee::create([
            'user_id' => $this->bendahara->id, 'department_id' => $deptFinance->id,
            'nip' => '100000002', 'name' => 'Ibu Bendahara', 'position' => 'Staff Keuangan', 'grade' => 'Staff',
        ]);

        $this->adminTravel = User::create(['email' => 'admintravel@gov.trip', 'password' => $pwd, 'is_active' => true]);
        Employee::create([
            'user_id' => $this->adminTravel->id, 'department_id' => $deptTravel->id,
            'nip' => '100000003', 'name' => 'Kang Tiket', 'position' => 'Staff Logistik', 'grade' => 'Staff',
        ]);

        $this->manager = User::create(['email' => 'manager@gov.trip', 'password' => $pwd, 'is_active' => true]);
        $empManager = Employee::create([
            'user_id' => $this->manager->id, 'department_id' => $deptProgram->id,
            'nip' => '100000004', 'name' => 'Bapak Kepala Bidang', 'position' => 'Kepala Bidang Program', 'grade' => 'Manager',
        ]);

        $this->pegawai = User::create(['email' => 'karyawan@gov.trip', 'password' => $pwd, 'is_active' => true]);
        Employee::create([
            'user_id' => $this->pegawai->id, 'department_id' => $deptProgram->id,
            'nip' => '100000005', 'name' => 'Andi Pratama', 'position' => 'Staf Perencanaan', 'grade' => 'Staff',
            'direct_supervisor_id' => $empManager->id,
        ]);

        // 3. Setup Cities & Policies
        $this->cityOrigin = RefCity::create(['province_name' => 'DKI Jakarta', 'city_name' => 'Jakarta Pusat']);
        $this->cityDest = RefCity::create(['province_name' => 'DI Yogyakarta', 'city_name' => 'Yogyakarta']);

        TravelPolicy::create([
            'destination_city_id' => $this->cityDest->id, 'grade_group' => 'Staff',
            'daily_allowance' => 380000, 'max_hotel_rate' => 650000, 'max_transport_rate' => 600000, 'effective_year' => 2026,
        ]);
    }

    /**
     * Test Step 1: Super Admin Configuration (Matrix, Cities, Policies).
     */
    public function test_superadmin_matrix_configuration(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/superadmin/akses');
        $response->assertStatus(200);
        $response->assertSee('Matriks Otorisasi Hak Akses Sistem');

        // Update Matrix: grant Pegawai bypass for setujui_pengajuan
        $updateResponse = $this->post('/superadmin/akses', [
            'matrix' => [
                'setujui_pengajuan' => ['Pegawai' => true, 'Atasan' => true],
            ],
        ]);
        $updateResponse->assertRedirect('/superadmin/akses');

        $matrixService = app(PermissionMatrixService::class);
        $this->assertTrue($matrixService->hasPermission($this->pegawai, 'setujui_pengajuan'));

        // Reset to default
        $this->post('/superadmin/akses/reset');
        $this->assertFalse($matrixService->hasPermission($this->pegawai, 'setujui_pengajuan'));
    }

    /**
     * Test Step 2 & 3: Pegawai creates Draft and Submits (Normal flow -> Menunggu Persetujuan Atasan).
     */
    public function test_pegawai_draft_and_normal_submit_workflow(): void
    {
        $this->actingAs($this->pegawai);

        // 1. Simpan Draft
        $draftResponse = $this->post('/pegawai/pengajuan', [
            'destination_city_id' => $this->cityDest->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'purpose' => 'Koordinasi program kerja regional',
            'action_type' => 'draft',
        ]);

        $draftResponse->assertRedirect('/pegawai/perjalanan');
        $this->assertDatabaseHas('travel_requests', [
            'purpose' => 'Koordinasi program kerja regional',
            'status' => 'Draft',
        ]);

        $travel = TravelRequest::where('purpose', 'Koordinasi program kerja regional')->first();
        $this->assertNotNull($travel);
        $this->assertNull($travel->sppd_number);

        // 2. Submit Draft ke Atasan (Normal Flow, No Bypass)
        $submitResponse = $this->post("/pegawai/pengajuan/{$travel->id}/submit");
        $submitResponse->assertRedirect('/pegawai/perjalanan');

        $travel->refresh();
        $this->assertEquals('Menunggu Persetujuan Atasan', $travel->status);
    }

    /**
     * Test Step 4: Super Admin Bypass Matrix (Fast-Track Bypass -> Status Disetujui + Auto SPPD).
     */
    public function test_fast_track_bypass_when_permission_granted(): void
    {
        // 1. Super Admin enables bypass for Pegawai
        $matrixService = app(PermissionMatrixService::class);
        $matrixService->updateMatrix([
            'setujui_pengajuan' => ['Pegawai' => true],
        ]);
        $this->assertTrue($matrixService->hasPermission($this->pegawai, 'setujui_pengajuan'));

        // 2. Pegawai submits travel request directly
        $this->actingAs($this->pegawai);

        $response = $this->post('/pegawai/pengajuan', [
            'destination_city_id' => $this->cityDest->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'purpose' => 'Fast track bypass travel request',
            'action_type' => 'submit',
        ]);

        $response->assertRedirect('/pegawai/perjalanan');

        $travel = TravelRequest::where('purpose', 'Fast track bypass travel request')->first();
        $this->assertNotNull($travel);
        $this->assertEquals('Disetujui', $travel->status);
        $this->assertNotNull($travel->sppd_number);
        $this->assertStringContainsString('SPPD', $travel->sppd_number);

        // Reset matrix
        $matrixService->resetToDefault();
    }

    /**
     * Test Step 5: Atasan Decision (Reject / Revision and Approval).
     */
    public function test_atasan_approval_and_revision_flow(): void
    {
        // Setup a pending travel request
        $travel = TravelRequest::create([
            'requester_employee_id' => $this->pegawai->employee->id,
            'origin_city_id' => $this->cityOrigin->id,
            'destination_city_id' => $this->cityDest->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'purpose' => 'Rapat pembahasan anggaran',
            'estimated_total_cost' => 3000000,
            'status' => 'Menunggu Persetujuan Atasan',
        ]);

        $this->actingAs($this->manager);

        // 1. Atasan minta revisi
        $rejectResponse = $this->post('/atasan/decision', [
            'submission_id' => $travel->id,
            'decision' => 'Perlu Revisi',
            'notes' => 'Agenda terlalu lama, harap dipadatkan menjadi 2 hari.',
        ]);

        $rejectResponse->assertRedirect('/atasan/inbox');
        $travel->refresh();
        $this->assertEquals('Direvisi', $travel->status);
        $this->assertEquals('Agenda terlalu lama, harap dipadatkan menjadi 2 hari.', $travel->rejection_reason);

        // 2. Pegawai perbaiki dan ajukan ulang
        $this->actingAs($this->pegawai);
        $updateResponse = $this->put("/pegawai/pengajuan/{$travel->id}", [
            'destination_city_id' => $this->cityDest->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(6)->format('Y-m-d'),
            'purpose' => 'Rapat pembahasan anggaran (sudah dipadatkan 2 hari)',
            'action_type' => 'submit',
        ]);
        $updateResponse->assertRedirect('/pegawai/perjalanan');

        $travel->refresh();
        $this->assertEquals('Menunggu Persetujuan Atasan', $travel->status);
        $this->assertNull($travel->rejection_reason);

        // 3. Atasan menyetujui
        $this->actingAs($this->manager);
        $approveResponse = $this->post('/atasan/decision', [
            'submission_id' => $travel->id,
            'decision' => 'Disetujui',
            'notes' => 'Disetujui, laksanakan tugas sebaik-baiknya.',
        ]);
        $approveResponse->assertRedirect('/atasan/inbox');

        $travel->refresh();
        $this->assertEquals('Disetujui', $travel->status);
        $this->assertNotNull($travel->sppd_number);
    }

    /**
     * Test Step 6 & 7: Admin Travel Logistics Processing (Reject & Input Logistics -> Siap Berangkat).
     */
    public function test_admin_travel_logistics_workflow(): void
    {
        $travel = TravelRequest::create([
            'requester_employee_id' => $this->pegawai->employee->id,
            'origin_city_id' => $this->cityOrigin->id,
            'destination_city_id' => $this->cityDest->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'purpose' => 'Workshop koordinasi',
            'estimated_total_cost' => 3000000,
            'sppd_number' => '090/SPPD-001/X/2026',
            'status' => 'Disetujui',
        ]);

        $this->actingAs($this->adminTravel);

        // 1. Admin Travel menolak karena kuota tiket habis
        $rejectLogistics = $this->post('/admin/logistics/reject', [
            'trip_id' => $travel->id,
            'reason' => 'Penerbangan penuh pada tanggal tersebut.',
        ]);
        $rejectLogistics->assertRedirect('/admin/perjalanan');

        $travel->refresh();
        $this->assertEquals('Direvisi', $travel->status);
        $this->assertEquals('Penerbangan penuh pada tanggal tersebut.', $travel->rejection_reason);

        // Pegawai ajukan kembali dan atasan setujui
        $travel->update(['status' => 'Disetujui', 'rejection_reason' => null]);

        // 2. Admin Travel input tiket & hotel -> Kunci budget -> Siap Berangkat
        $storeLogistics = $this->post('/admin/logistics', [
            'trip_id' => $travel->id,
            'transport_type' => 'Garuda Indonesia GA-214 PP',
            'booking_code' => 'GA-9988112',
            'hotel_name' => 'Hotel Grand Mercure Yogyakarta',
            'departure_datetime' => now()->addDays(5)->format('Y-m-d').'T08:00',
            'return_datetime' => now()->addDays(7)->format('Y-m-d').'T17:00',
            'estimated_total_cost' => 2850000,
        ]);
        $storeLogistics->assertRedirect('/admin/sppd');

        $travel->refresh();
        $this->assertEquals('Siap Berangkat', $travel->status);
        $this->assertEquals(2850000, (int) $travel->advance_payment);

        // Cetak SPPD Digital PDF
        $sppdPdfResponse = $this->get("/travel-requests/{$travel->id}/download-sppd");
        $sppdPdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $sppdPdfResponse->headers->get('content-type'));
    }

    /**
     * Test Step 8 & 9 & 10: LPJ Submission, Finance Verification, and Final Audit PDF Download.
     */
    public function test_finance_lpj_verification_and_final_audit(): void
    {
        $travel = TravelRequest::create([
            'requester_employee_id' => $this->pegawai->employee->id,
            'origin_city_id' => $this->cityOrigin->id,
            'destination_city_id' => $this->cityDest->id,
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->subDays(3)->format('Y-m-d'),
            'purpose' => 'Penyuluhan dan bimbingan teknis',
            'estimated_total_cost' => 2850000,
            'advance_payment' => 2850000,
            'sppd_number' => '090/SPPD-002/X/2026',
            'status' => 'Siap Berangkat',
        ]);

        // 1. Pegawai mengunggah LPJ
        $this->actingAs($this->pegawai);
        $lpjResponse = $this->post('/pegawai/lpj', [
            'travel_request_id' => $travel->id,
            'total_real_expense' => 2700000,
            'activity_summary' => 'Kegiatan bimtek selesai 100%. Sisa Rp 150.000 disetor ke kas.',
        ]);
        $lpjResponse->assertRedirect('/pegawai/perjalanan');

        $travel->refresh();
        $this->assertEquals('Menunggu Verifikasi LPJ', $travel->status);

        // 2. Bendahara verifikasi: minta revisi
        $this->actingAs($this->bendahara);
        $rejectLpj = $this->post('/bendahara/verifikasi', [
            'lpj_id' => $travel->id,
            'action' => 'REJECT',
            'notes' => 'Kuitansi hotel buram, mohon unggah ulang scan yang jelas.',
        ]);
        $rejectLpj->assertRedirect('/bendahara/verifikasi');

        $travel->refresh();
        $this->assertEquals('Revisi LPJ', $travel->status);
        $this->assertEquals('Kuitansi hotel buram, mohon unggah ulang scan yang jelas.', $travel->rejection_reason);

        // 3. Pegawai upload ulang LPJ
        $this->actingAs($this->pegawai);
        $this->post('/pegawai/lpj', [
            'travel_request_id' => $travel->id,
            'total_real_expense' => 2700000,
            'activity_summary' => 'Scan kuitansi hotel sudah diganti dengan kualitas jelas.',
        ]);
        $travel->refresh();
        $this->assertEquals('Menunggu Verifikasi LPJ', $travel->status);

        // 4. Bendahara sahkan LPJ -> Status: Selesai
        $this->actingAs($this->bendahara);
        $approveLpj = $this->post('/bendahara/verifikasi', [
            'lpj_id' => $travel->id,
            'action' => 'APPROVE',
            'notes' => 'Bukti kuitansi dan pengembalian kas sisa Rp 150.000 telah valid.',
        ]);
        $approveLpj->assertRedirect('/bendahara/verifikasi');

        $travel->refresh();
        $this->assertEquals('Selesai', $travel->status);

        // 5. Cetak LPJ Final & Audit Trail PDF
        $finalPdfResponse = $this->get("/travel-requests/{$travel->id}/download-lpj");
        $finalPdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $finalPdfResponse->headers->get('content-type'));
    }
}
