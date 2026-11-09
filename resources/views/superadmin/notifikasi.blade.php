<x-layouts.app title="Notifikasi Super Admin - Gov.Trip" role="Super Admin" headerTitle="Notifikasi">
    @php
        $notifications = [
            [
                'title' => 'Percobaan Login Gagal Terdeteksi',
                'description' => '3x salah password dari IP 192.168.1.45',
                'time' => '1 jam lalu',
                'unread' => true,
                'category' => 'danger',
            ],
            [
                'title' => 'Backup Otomatis Basis Data Berhasil',
                'description' => 'Arsip harian database Gov.Trip tersimpan aman',
                'time' => '04.00 WIB',
                'unread' => false,
                'category' => 'success',
            ],
            [
                'title' => 'Akun Baru Menunggu Aktivasi',
                'description' => 'Staf Bidang Perencanaan mengajukan registrasi',
                'time' => 'Kemarin',
                'unread' => false,
                'category' => 'info',
            ],
        ];
    @endphp

    <x-notification-list :notifications="$notifications" title="Notifikasi Keamanan & Sistem" description="Pemberitahuan administratif dan status integritas sistem" />
</x-layouts.app>
