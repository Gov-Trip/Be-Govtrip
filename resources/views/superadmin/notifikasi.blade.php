<x-layouts.app title="Notifikasi Super Admin - Gov.Trip" role="Super Admin" headerTitle="Notifikasi">
    <x-notification-list :notifications="$notifications ?? []" title="Notifikasi Keamanan & Sistem" description="Pemberitahuan administratif dan status integritas sistem" />
</x-layouts.app>
