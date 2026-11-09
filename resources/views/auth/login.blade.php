<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - Gov.Trip</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans bg-[#123B72] text-[#1E293B]">
    <div class="min-h-screen w-full bg-[#123B72] flex items-center justify-center p-4 py-8 sm:py-12">
        <div class="w-full max-w-[440px] bg-white rounded-[18px] p-8 shadow-[0px_12px_40px_0px_rgba(0,0,0,0.25)] flex flex-col items-center">
            <!-- Logo Gov.Trip -->
            <div class="relative w-[120px] h-[120px] flex items-center justify-center">
                <img src="/images/logo.png" alt="Gov.Trip Logo" class="w-[120px] h-[120px] object-contain" />
            </div>

            <!-- Title & Subtitle -->
            <div class="text-center mt-2 mb-6">
                <h2 class="text-[22px] font-bold text-[#123B72] tracking-tight">
                    Masuk ke Gov.Trip
                </h2>
                <p class="text-[12.5px] text-[#64748B] mt-1">
                    Sistem Informasi Perjalanan Dinas Pemerintahan
                </p>
            </div>

            @if(session('error'))
                <div class="w-full mb-4 p-3 rounded-lg text-[12.5px] font-medium bg-[#FBE8E8] text-[#B02A2A] border border-[#f5c6cb]">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="w-full mb-4 p-3 rounded-lg text-[12.5px] font-medium bg-[#DFF5F4] text-[#0B7A77] border border-[#b2dfdb]">
                    {{ session('success') }}
                </div>
            @endif

            <div id="status-box" class="w-full mb-4 hidden p-3 rounded-lg text-[12.5px] font-medium"></div>

            <form action="{{ route('login.post') }}" method="POST" id="loginForm" class="w-full flex flex-col gap-4">
                @csrf
                <!-- NIP / Email Input -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-[12.5px] font-medium text-[#1E293B]">
                        NIP atau Email Dinas
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-[#64748B] pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="email"
                            name="email"
                            value="{{ old('email', 'karyawan@gov.trip') }}"
                            required
                            placeholder="1988... / nama@instansi.go.id"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13px] text-[#1E293B] placeholder-[#94A3B8] focus:outline-none focus:border-[#0066B3] focus:ring-1 focus:ring-[#0066B3] transition-all"
                        />
                    </div>
                </div>

                <!-- Password Input -->
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-[12.5px] font-medium text-[#1E293B]">
                        Kata Sandi
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-[#64748B] pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            value="password123"
                            required
                            placeholder="Masukkan kata sandi"
                            class="w-full pl-10 pr-10 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13px] text-[#1E293B] placeholder-[#94A3B8] focus:outline-none focus:border-[#0066B3] focus:ring-1 focus:ring-[#0066B3] transition-all"
                        />
                        <button
                            type="button"
                            onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';"
                            class="absolute right-3.5 text-[#64748B] hover:text-[#1E293B] cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-[#E8EEF3] text-[#0075FF] accent-[#0075FF] cursor-pointer"
                        />
                        <span class="text-[#64748B] text-[12.5px] leading-[19.38px]">
                            Ingat saya
                        </span>
                    </label>

                    <button
                        type="button"
                        onclick="alert('Silakan hubungi administrator instansi Anda untuk mereset kata sandi.')"
                        class="text-[#0066B3] text-[12.5px] font-medium leading-[19.38px] hover:underline transition-colors cursor-pointer"
                    >
                        Lupa kata sandi?
                    </button>
                </div>

                <!-- Button Masuk -->
                <button
                    type="submit"
                    class="w-full py-2.5 px-4 bg-[#0066B3] hover:bg-[#005596] active:bg-[#004478] text-white text-[13px] font-semibold leading-[20.15px] rounded-[10px] border border-[#0066B3] transition-all duration-150 shadow-sm flex items-center justify-center cursor-pointer"
                >
                    Masuk
                </button>
            </form>

            <!-- Demo Fast Navigation per Role -->
            <div class="w-full mt-5 pt-4 border-t border-[#E8EEF3]">
                <span class="text-[11.5px] text-[#64748B] block text-center mb-2 font-medium">
                    Masuk Cepat Demo Peran:
                </span>
                <div class="grid grid-cols-2 gap-2 text-center text-[12px]">
                    <button
                        type="button"
                        onclick="setDemoAndSubmit('admintravel@gov.trip', 'password123')"
                        class="py-1.5 px-2 bg-[#F1F7FB] hover:bg-[#E3F0FA] text-[#0066B3] rounded-lg font-medium transition-colors cursor-pointer"
                    >
                        Admin Travel
                    </button>
                    <button
                        type="button"
                        onclick="setDemoAndSubmit('karyawan@gov.trip', 'password123')"
                        class="py-1.5 px-2 bg-[#F1F7FB] hover:bg-[#E3F0FA] text-[#0066B3] rounded-lg font-medium transition-colors cursor-pointer"
                    >
                        Pegawai
                    </button>
                    <button
                        type="button"
                        onclick="setDemoAndSubmit('manager@gov.trip', 'password123')"
                        class="py-1.5 px-2 bg-[#F1F7FB] hover:bg-[#E3F0FA] text-[#0066B3] rounded-lg font-medium transition-colors cursor-pointer"
                    >
                        Atasan
                    </button>
                    <button
                        type="button"
                        onclick="setDemoAndSubmit('bendahara@gov.trip', 'password123')"
                        class="py-1.5 px-2 bg-[#FFF1D1] hover:bg-[#fee7b3] text-[#8A5A00] rounded-lg font-semibold transition-colors cursor-pointer"
                    >
                        Bendahara
                    </button>
                    <button
                        type="button"
                        onclick="setDemoAndSubmit('admin@gov.trip', 'password123')"
                        class="col-span-2 py-1.5 px-2 bg-[#123B72] hover:bg-[#0066B3] text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                        Super Administrator
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDemoAndSubmit(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
            document.getElementById('loginForm').submit();
        }
    </script>
</body>
</html>
