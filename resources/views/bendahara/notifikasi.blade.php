<x-layouts.app title="Notifikasi Bendahara - Gov.Trip" role="Bendahara" headerTitle="Notifikasi">
    @php
        $notifications = [
            [
                'title' => 'Berkas LPJ Baru Diunggah: Bagas (Jakarta)',
                'description' => 'Realisasi Rp 3.655.000 menunggu pengecekan fisik',
                'time' => '30 menit lalu',
                'unread' => true,
                'category' => 'warning',
            ],
            [
                'title' => 'Pengajuan Logistik Siap Pencairan: Fajar (Balikpapan)',
                'description' => 'Estimasi biaya Rp 5.400.000 telah disetujui atasan',
                'time' => '2 jam lalu',
                'unread' => true,
                'category' => 'info',
            ],
            [
                'title' => 'Setoran Sisa Uang Muka Rp 200.000 Diterima',
                'description' => 'Dari Citra Lestari untuk SPPD 090/SPPD-005/X/2026',
                'time' => 'Kemarin',
                'unread' => false,
                'category' => 'success',
            ],
        ];
    @endphp

    <x-notification-list :notifications="$notifications" title="Notifikasi Keuangan & LPJ" description="Pemberitahuan terkait berkas pertanggungjawaban dan permohonan uang muka dinas" />
</x-layouts.app>
