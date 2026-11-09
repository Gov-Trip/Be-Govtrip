<x-layouts.app title="Pengajuan Perjalanan Dinas - Gov.Trip" role="Pegawai" headerTitle="Pengajuan Perjalanan Dinas">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Form Input (col-span-8) -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <x-card>
                <div class="border-b border-[#E8EEF3] pb-4 mb-6">
                    <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Formulir Pengajuan Baru</h3>
                    <p class="text-[#64748B] text-[12.5px] mt-0.5">Lengkapi informasi rencana perjalanan dinas Anda untuk diteruskan ke atasan</p>
                </div>

                @if(session('success'))
                    <div class="mb-5 p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('pegawai.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Tipe Perjalanan</label>
                            <select name="trip_type" class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">
                                <option value="Luar kota">Luar Kota</option>
                                <option value="Dalam kota">Dalam Kota</option>
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Kota Tujuan</label>
                            <select name="destination_city_id" id="destCity" class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">
                                @foreach($cities ?? [] as $city)
                                    <option value="{{ $city->id }}">{{ $city->city_name }} ({{ $city->province_name }})</option>
                                @endforeach
                                @if(empty($cities) || count($cities) === 0)
                                    <option value="2">Bandung (Jawa Barat)</option>
                                    <option value="3">Surabaya (Jawa Timur)</option>
                                    <option value="4">Yogyakarta (DI Yogyakarta)</option>
                                    <option value="5">Denpasar (Bali)</option>
                                    <option value="1">Jakarta Pusat (DKI Jakarta)</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Tanggal Berangkat</label>
                            <input type="date" name="start_date" value="2026-10-25" required
                                   class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Tanggal Kembali</label>
                            <input type="date" name="end_date" value="2026-10-27" required
                                   class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Moda Transportasi</label>
                            <select name="transport" class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">
                                <option value="Kereta api">Kereta Api</option>
                                <option value="Pesawat">Pesawat Terbang</option>
                                <option value="Kendaraan dinas">Kendaraan Dinas Operasional</option>
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Unit Kerja / Bidang</label>
                            <input type="text" name="work_unit" value="Bidang Perencanaan" readonly
                                   class="w-full px-3.5 py-2.5 text-[13.5px] text-[#64748B] bg-slate-50 border border-[#E8EEF3] rounded-[10px]" />
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Maksud / Keperluan Perjalanan Dinas</label>
                        <textarea name="purpose" rows="3" required placeholder="Jelaskan secara rinci tujuan dan agenda dinas..."
                                  class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">Rapat koordinasi penyusunan program regional bersama kementerian dan dinas terkait.</textarea>
                    </div>

                    <!-- Estimasi Biaya Grid -->
                    <div class="pt-2">
                        <label class="text-[#123B72] text-[13.5px] font-semibold mb-2 block">Estimasi Rincian Anggaran (SBM 2026)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="flex flex-col">
                                <span class="text-[12px] text-[#64748B] mb-1">Tiket Transportasi</span>
                                <input type="number" id="costTrans" name="cost_transport" value="900000" oninput="recalcBudget()"
                                       class="px-3 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[12px] text-[#64748B] mb-1">Akomodasi Hotel</span>
                                <input type="number" id="costHotel" name="cost_accommodation" value="1600000" oninput="recalcBudget()"
                                       class="px-3 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[12px] text-[#64748B] mb-1">Uang Harian</span>
                                <input type="number" id="costDaily" name="cost_daily" value="1350000" oninput="recalcBudget()"
                                       class="px-3 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Surat Undangan / Dasar Penugasan (Opsional)</label>
                        <div class="border-2 border-dashed border-[#1684C4] bg-[#E3F0FA] hover:bg-[#d8ecf9] rounded-[12px] p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center"
                             onclick="document.getElementById('fileInput').click()">
                            <input type="file" id="fileInput" name="attachment" class="hidden" onchange="document.getElementById('fileNameSpan').innerText = this.files[0]?.name || ''" />
                            <div class="w-10 h-10 rounded-full bg-white text-[#0066B3] flex items-center justify-center shadow-xs mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0066B3]">Pilih berkas atau tarik ke sini</span>
                            <span class="text-[11.5px] text-[#64748B] mt-0.5">Format PDF, maks. 5 MB</span>
                            <span id="fileNameSpan" class="text-[12px] font-medium text-[#123B72] mt-2"></span>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E8EEF3] mt-2">
                        <a href="/pegawai/draft" class="px-4 py-2 text-[13px] rounded-[10px] text-[#1E293B] border border-[#E8EEF3] hover:bg-[#F1F7FB] transition-all">
                            Simpan Draft
                        </a>
                        <button type="submit" class="px-5 py-2 text-[13px] font-semibold rounded-[10px] bg-[#0066B3] hover:bg-[#005596] text-white shadow-sm transition-all cursor-pointer">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Right: Summary & Workflow (col-span-4) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card>
                <div class="border-b border-[#E8EEF3] pb-3 mb-4">
                    <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Total Estimasi Anggaran</h3>
                    <p class="text-[#64748B] text-[12px] mt-0.5">Sesuai alokasi pagu DPA dinas</p>
                </div>

                <div class="flex flex-col gap-2.5 text-[13px]">
                    <div class="flex justify-between text-[#64748B]">
                        <span>Tiket Transportasi</span>
                        <span id="labelTrans" class="font-medium text-[#1E293B]">Rp 900.000</span>
                    </div>
                    <div class="flex justify-between text-[#64748B]">
                        <span>Akomodasi</span>
                        <span id="labelHotel" class="font-medium text-[#1E293B]">Rp 1.600.000</span>
                    </div>
                    <div class="flex justify-between text-[#64748B]">
                        <span>Uang Harian</span>
                        <span id="labelDaily" class="font-medium text-[#1E293B]">Rp 1.350.000</span>
                    </div>
                    <div class="pt-3 border-t border-[#E8EEF3] flex justify-between items-center">
                        <span class="font-semibold text-[#123B72]">Total Estimasi</span>
                        <span id="labelTotal" class="font-bold text-[18px] text-[#0066B3]">Rp 3.850.000</span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="border-b border-[#E8EEF3] pb-3 mb-4">
                    <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Alur Verifikasi</h3>
                    <p class="text-[#64748B] text-[12px] mt-0.5">Proses persetujuan bertingkat</p>
                </div>

                @php
                    $steps = [
                        ['label' => 'Pengajuan Diajukan', 'sublabel' => 'Oleh Pegawai', 'status' => 'completed'],
                        ['label' => 'Persetujuan Atasan', 'sublabel' => 'Kepala Bidang Program', 'status' => 'upcoming'],
                        ['label' => 'Logistik & Tiket', 'sublabel' => 'Admin Travel', 'status' => 'upcoming'],
                        ['label' => 'Penerbitan SPPD', 'sublabel' => 'Kepala Dinas', 'status' => 'upcoming'],
                    ];
                @endphp
                <x-stepper :steps="$steps" orientation="vertical" />
            </x-card>
        </div>
    </div>

    <script>
        function recalcBudget() {
            const trans = parseInt(document.getElementById('costTrans').value) || 0;
            const hotel = parseInt(document.getElementById('costHotel').value) || 0;
            const daily = parseInt(document.getElementById('costDaily').value) || 0;
            const total = trans + hotel + daily;

            const fmt = (n) => 'Rp ' + n.toLocaleString('id-ID');
            document.getElementById('labelTrans').innerText = fmt(trans);
            document.getElementById('labelHotel').innerText = fmt(hotel);
            document.getElementById('labelDaily').innerText = fmt(daily);
            document.getElementById('labelTotal').innerText = fmt(total);
        }
    </script>
</x-layouts.app>
