<x-layouts.app title="Notifikasi Admin Travel - Gov.Trip" role="Admin" headerTitle="Notifikasi">
    @php
        $notifications = [
            [
                'title' => 'SPPD 090/090/SPPD/X/2026 Perlu Tiket Pesawat',
                'description' => 'Fajar tujuan Balikpapan berangkat 2 Nov',
                'time' => '15 menit lalu',
                'unread' => true,
                'category' => 'warning',
            ],
            [
                'title' => 'Liyana Melakukan Konfirmasi Check-in Yogyakarta',
                'description' => 'Tiba di stasiun Tugu sesuai jadwal',
                'time' => '1 jam lalu',
                'unread' => true,
                'category' => 'info',
            ],
            [
                'title' => 'Surat Tugas Rudi (Bandung) Siap Ditindaklanjuti',
                'description' => 'Disetujui oleh Kepala Bidang',
                'time' => 'Kemarin',
                'unread' => false,
                'category' => 'success',
            ],
        ];
    @endphp

    <x-notification-list :notifications="$notifications" title="Notifikasi Admin Travel & Logistik" description="Pemberitahuan terkait penerbitan tiket dan monitoring perjalanan dinas" />
</x-layouts.app>
