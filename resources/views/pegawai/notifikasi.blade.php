<x-layouts.app title="Notifikasi Pegawai - Gov.Trip" role="Pegawai" headerTitle="Notifikasi">
    <x-notification-list :notifications="$notifications ?? []" title="Semua Notifikasi" description="Pembaruan status perjalanan dinas dan audit aktivitas Anda" />
</x-layouts.app>
