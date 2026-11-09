@props([
    'title' => 'Gov.Trip - Sistem Perjalanan Dinas',
    'role' => 'Pegawai',
    'headerTitle' => null,
])

<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans bg-[#F1F7FB] text-[#1E293B]">
    <div class="min-h-screen flex bg-[#F1F7FB]">
        <!-- Desktop Sidebar -->
        <div class="hidden lg:block shrink-0 sticky top-0 h-screen">
            <x-app-sidebar :role="$role" />
        </div>

        <!-- Mobile Drawer Overlay -->
        <div id="mobile-drawer" class="fixed inset-0 z-50 lg:hidden flex hidden">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"
                 onclick="window.GovTrip?.closeMobileSidebar()"></div>
            <div class="relative z-10 w-[252px] bg-[#123B72] h-full shadow-2xl">
                <x-app-sidebar :role="$role" />
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            @if($headerTitle)
                <x-app-header :title="$headerTitle" :role="$role" />
            @endif

            <main class="p-6 sm:p-8 flex flex-col gap-6 max-w-[1440px] w-full">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
