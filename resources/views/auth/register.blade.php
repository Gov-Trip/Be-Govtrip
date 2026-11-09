<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - Gov.Trip</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans bg-[#123B72] text-[#1E293B]">
    <div class="min-h-screen w-full bg-[#123B72] flex items-center justify-center p-4 py-8 sm:py-12">
        <div class="w-full max-w-[480px] bg-white rounded-[18px] p-8 shadow-[0px_12px_40px_0px_rgba(0,0,0,0.25)] flex flex-col items-center">
            <!-- Logo Gov.Trip -->
            <div class="relative w-[100px] h-[100px] flex items-center justify-center">
                <img src="/images/logo.png" alt="Gov.Trip Logo" class="w-[100px] h-[100px] object-contain" />
            </div>

            <div class="text-center mt-2 mb-6">
                <h2 class="text-[22px] font-bold text-[#123B72] tracking-tight">
                    Daftar Akun Baru
                </h2>
                <p class="text-[12.5px] text-[#64748B] mt-1">
                    Registrasi akun pegawai untuk sistem perjalanan dinas
                </p>
            </div>

            <form action="{{ route('register.post') }}" method="POST" class="w-full flex flex-col gap-3.5">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="text-[12.5px] font-medium text-[#1E293B]">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Nama lengkap beserta gelar"
                           class="w-full px-3.5 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13px] text-[#1E293B] focus:outline-none focus:border-[#0066B3] focus:ring-1 focus:ring-[#0066B3]" />
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[12.5px] font-medium text-[#1E293B]">NIP</label>
                    <input type="text" name="nip" required placeholder="1988..."
                           class="w-full px-3.5 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13px] text-[#1E293B] focus:outline-none focus:border-[#0066B3] focus:ring-1 focus:ring-[#0066B3]" />
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[12.5px] font-medium text-[#1E293B]">Unit Kerja</label>
                    <select name="department_id" class="w-full px-3.5 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13px] text-[#1E293B] focus:outline-none focus:border-[#0066B3]">
                        <option value="4">Perencanaan & Program</option>
                        <option value="2">Divisi Keuangan</option>
                        <option value="3">Logistik & Akomodasi</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[12.5px] font-medium text-[#1E293B]">Email Dinas</label>
                    <input type="email" name="email" required placeholder="nama@instansi.go.id"
                           class="w-full px-3.5 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13px] text-[#1E293B] focus:outline-none focus:border-[#0066B3] focus:ring-1 focus:ring-[#0066B3]" />
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[12.5px] font-medium text-[#1E293B]">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="Minimal 8 karakter"
                           class="w-full px-3.5 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13px] text-[#1E293B] focus:outline-none focus:border-[#0066B3] focus:ring-1 focus:ring-[#0066B3]" />
                </div>

                <button type="submit"
                        class="w-full mt-2 py-2.5 px-4 bg-[#0066B3] hover:bg-[#005596] active:bg-[#004478] text-white text-[13px] font-semibold rounded-[10px] transition-all cursor-pointer">
                    Daftar Sekarang
                </button>
            </form>

            <div class="flex items-center justify-center pt-4 text-[12.5px]">
                <span class="text-[#64748B]">Sudah punya akun?&nbsp;</span>
                <a href="/login" class="text-[#0066B3] font-medium hover:underline">
                    Masuk
                </a>
            </div>
        </div>
    </div>
</body>
</html>
