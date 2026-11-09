<x-layouts.app title="Notifikasi Pegawai - Gov.Trip" role="Pegawai" headerTitle="Notifikasi">
    @php
        $notifications = [
            [
                'title' => 'Pengajuan Bandung disetujui Atasan',
                'description' => 'Diteruskan ke Kepala Bidang Program',
                'time' => '1 jam lalu',
                'unread' => true,
                'category' => 'success',
            ],
            [
                'title' => 'LPJ Makassar batas 23 Sep',
                'description' => 'Unggah laporan kegiatan dan dokumentasi',
                'time' => '3 jam lalu',
                'unread' => true,
                'category' => 'warning',
            ],
            [
                'title' => 'SPPD Yogyakarta siap diunduh',
                'description' => 'Nomor 090/087/SPPD/IX/2026',
                'time' => 'Kemarin',
                'unread' => true,
                'category' => 'info',
            ],
            [
                'title' => 'Draft Medan belum diajukan',
                'description' => 'Lengkapi tanggal dan rincian biaya',
                'time' => '2 hari lalu',
                'unread' => false,
                'category' => 'neutral',
            ],
        ];
    @endphp

    <x-notification-list :notifications="$notifications" title="Semua Notifikasi" description="Pembaruan status perjalanan dinas dan tenggat waktu Anda" />
</x-layouts.app>
