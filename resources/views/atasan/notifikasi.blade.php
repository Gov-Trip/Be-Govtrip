<x-layouts.app title="Notifikasi Atasan - Gov.Trip" role="Atasan" headerTitle="Notifikasi">
    <x-notification-list :notifications="$notifications ?? []" title="Notifikasi Persetujuan & Penugasan Tim" description="Pemberitahuan terkait pengajuan bawahan dan perkembangan penugasan dinas" />
</x-layouts.app>
