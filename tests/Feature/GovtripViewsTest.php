<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GovtripViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_pages_render_successfully(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    public function test_pegawai_pages_render_successfully(): void
    {
        $this->get('/pegawai')->assertStatus(200);
        $this->get('/pegawai/pengajuan')->assertStatus(200);
        $this->get('/pegawai/perjalanan')->assertStatus(200);
        $this->get('/pegawai/sppd')->assertStatus(200);
        $this->get('/pegawai/lpj')->assertStatus(200);
        $this->get('/pegawai/draft')->assertStatus(200);
        $this->get('/pegawai/notifikasi')->assertStatus(200);
        $this->get('/pegawai/profil')->assertStatus(200);
    }

    public function test_atasan_pages_render_successfully(): void
    {
        $this->get('/atasan')->assertStatus(200);
        $this->get('/atasan/inbox')->assertStatus(200);
        $this->get('/atasan/tim')->assertStatus(200);
        $this->get('/atasan/riwayat')->assertStatus(200);
        $this->get('/atasan/notifikasi')->assertStatus(200);
    }

    public function test_bendahara_pages_render_successfully(): void
    {
        $this->get('/bendahara')->assertStatus(200);
        $this->get('/bendahara/verifikasi')->assertStatus(200);
        $this->get('/bendahara/pencairan')->assertStatus(200);
        $this->get('/bendahara/anggaran')->assertStatus(200);
        $this->get('/bendahara/logistik')->assertStatus(200);
        $this->get('/bendahara/lpj')->assertStatus(200);
        $this->get('/bendahara/riwayat')->assertStatus(200);
        $this->get('/bendahara/notifikasi')->assertStatus(200);
    }

    public function test_admin_travel_pages_render_successfully(): void
    {
        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/pegawai')->assertStatus(200);
        $this->get('/admin/perjalanan')->assertStatus(200);
        $this->get('/admin/dokumen')->assertStatus(200);
        $this->get('/admin/sppd')->assertStatus(200);
        $this->get('/admin/monitoring')->assertStatus(200);
        $this->get('/admin/notifikasi')->assertStatus(200);
    }

    public function test_superadmin_pages_render_successfully(): void
    {
        $this->get('/superadmin')->assertStatus(200);
        $this->get('/superadmin/pengguna')->assertStatus(200);
        $this->get('/superadmin/akses')->assertStatus(200);
        $this->get('/superadmin/konfigurasi')->assertStatus(200);
        $this->get('/superadmin/audit')->assertStatus(200);
        $this->get('/superadmin/unit')->assertStatus(200);
        $this->get('/superadmin/notifikasi')->assertStatus(200);
    }
}
