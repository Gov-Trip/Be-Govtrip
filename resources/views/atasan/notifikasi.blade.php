<x-layouts.app title="Notifikasi Atasan - Gov.Trip" role="Atasan" headerTitle="Notifikasi">
    @php
        $notifications = [
            [
                'title' => 'Pengajuan Perjalanan Baru: Rudi (Surabaya)',
                'description' => 'Membutuhkan persetujuan rencana tanggal 12–14 Okt',
                'time' => '10 menit lalu',
                'unread' => true,
                'category' => 'warning',
            ],
            [
                'title' => 'LPJ Bagas (Jakarta) Telah Diverifikasi Finance',
                'description' => 'Realisasi Rp 3.655.000 dinyatakan valid',
                'time' => '1 jam lalu',
                'unread' => true,
                'category' => 'success',
            ],
            [
                'title' => 'Liyana Melakukan Check-in di Yogyakarta',
                'description' => 'Hari ke-1 pelaksanaan kegiatan sosialisasi',
                'time' => 'Kemarin',
                'unread' => false,
                'category' => 'info',
            ],
        ];
    @endphp

    <x-notification-list :notifications="$notifications" title="Notifikasi Persetujuan & Penugasan Tim" description="Pemberitahuan terkait pengajuan bawahan dan perkembangan penugasan dinas" />
</x-layouts.app>
