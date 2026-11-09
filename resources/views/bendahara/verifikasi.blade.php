<x-layouts.app title="Verifikasi Berkas LPJ - Gov.Trip" role="Bendahara" headerTitle="Verifikasi Berkas LPJ">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Antrean LPJ (col-span-4) -->
        <div class="lg:col-span-4 flex flex-col gap-3">
            <span class="text-[13px] text-[#64748B] font-medium px-1">Antrean Masuk (3)</span>

            <div onclick="selectLpj('bagas')" id="card-bagas" class="lpj-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Bagas &middot; Jakarta</span>
                    <x-badge status="Menunggu">Perlu verifikasi</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">LPJ-2026/044</span>
                <div class="flex justify-between items-center mt-2 text-[12px]">
                    <span class="text-[#64748B]">Realisasi:</span>
                    <span class="font-semibold text-[#0066B3]">Rp 3.655.000</span>
                </div>
            </div>

            <div onclick="selectLpj('dewi')" id="card-dewi" class="lpj-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Dewi &middot; Surabaya</span>
                    <x-badge status="Menunggu">Perlu verifikasi</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">LPJ-2026/045</span>
                <div class="flex justify-between items-center mt-2 text-[12px]">
                    <span class="text-[#64748B]">Realisasi:</span>
                    <span class="font-semibold text-[#0066B3]">Rp 5.240.000</span>
                </div>
            </div>

            <div onclick="selectLpj('nadia')" id="card-nadia" class="lpj-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Nadia &middot; Bogor</span>
                    <x-badge status="Menunggu">Perlu verifikasi</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">LPJ-2026/046</span>
                <div class="flex justify-between items-center mt-2 text-[12px]">
                    <span class="text-[#64748B]">Realisasi:</span>
                    <span class="font-semibold text-[#0066B3]">Rp 1.520.000</span>
                </div>
            </div>
        </div>

        <!-- Right: Detail Pemeriksaan (col-span-8) -->
        <div class="lg:col-span-8">
            <x-card padding="lg">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E8EEF3] gap-2 mb-5">
                    <div>
                        <h3 id="lpjTitle" class="text-[17px] font-bold text-[#123B72]">LPJ-2026/044 &middot; Bagas Wicaksono</h3>
                        <p id="lpjDest" class="text-[12.5px] text-[#64748B]">Perjalanan Dinas ke Jakarta</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge status="Menunggu">Pemeriksaan Berkas</x-badge>
                        <a href="/api/finance/verifications/1/lpj/download" target="_blank"
                           class="px-3 py-1.5 text-[12px] font-medium text-[#0066B3] bg-[#E3F0FA] hover:bg-[#cde4f7] rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Unduh Laporan Audit</span>
                        </a>
                    </div>
                </div>

                <!-- Comparison Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                    <div class="p-3.5 rounded-[10px] bg-[#F1F7FB] border border-[#E8EEF3]">
                        <span class="text-[11.5px] text-[#64748B] block font-medium">Uang Muka Diterima</span>
                        <span id="lpjPagu" class="text-[16px] font-bold text-[#123B72] mt-0.5 block">Rp 3.780.000</span>
                    </div>
                    <div class="p-3.5 rounded-[10px] bg-[#DFF5F4] border border-[#a2dedc]">
                        <span class="text-[11.5px] text-[#0B7A77] block font-medium">Realisasi Pengeluaran Riil</span>
                        <span id="lpjReal" class="text-[16px] font-bold text-[#0B7A77] mt-0.5 block">Rp 3.655.000</span>
                    </div>
                    <div class="p-3.5 rounded-[10px] bg-[#FFF1D1] border border-[#ffe099]">
                        <span class="text-[11.5px] text-[#8A5A00] block font-medium">Sisa Uang Muka (Setor Kas)</span>
                        <span id="lpjDiff" class="text-[16px] font-bold text-[#8A5A00] mt-0.5 block">Rp 125.000</span>
                    </div>
                </div>

                <!-- Checklist Bukti Fisik -->
                <div class="border-t border-[#E8EEF3] pt-4 mb-5">
                    <span class="text-[13px] font-semibold text-[#123B72] block mb-3">Status Verifikasi Bukti Fisik SPJ:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[12.5px]">
                        <div class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8EEF3]">
                            <span class="text-[#1E293B]">Tiket / Boarding Pass Resmi</span>
                            <span id="fileTiket"><x-badge status="Lengkap">Valid</x-badge></span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8EEF3]">
                            <span class="text-[#1E293B]">Kuitansi Penginapan / Hotel</span>
                            <span id="fileHotel"><x-badge status="Lengkap">Valid</x-badge></span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8EEF3]">
                            <span class="text-[#1E293B]">Laporan Hasil Perjalanan Dinas</span>
                            <span id="fileLaporan"><x-badge status="Lengkap">Valid</x-badge></span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8EEF3]">
                            <span class="text-[#1E293B]">Foto Dokumentasi Kegiatan</span>
                            <span id="fileFoto"><x-badge status="Belum lengkap">Kurang</x-badge></span>
                        </div>
                    </div>
                </div>

                <!-- Action Form -->
                <form action="{{ route('bendahara.verifikasi.post') }}" method="POST" class="pt-4 border-t border-[#E8EEF3] flex flex-col gap-3">
                    @csrf
                    <input type="hidden" name="lpj_id" id="formLpjId" value="1" />
                    <input type="hidden" name="action" id="formLpjAction" value="APPROVE" />

                    <div>
                        <label class="text-[#64748B] text-[12px] font-medium block mb-1">Catatan Audit Keuangan</label>
                        <textarea name="notes" rows="2" placeholder="Catatan hasil verifikasi kuitansi dan bukti transfer..."
                                  class="w-full px-3 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">Bukti kuitansi dan tiket telah dicocokkan dengan SBM 2026. Sisa uang muka sebesar Rp 125.000 telah disetorkan kembali ke kas daerah.</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="submit" onclick="document.getElementById('formLpjAction').value = 'REJECT'"
                                class="px-3.5 py-2 text-[12.5px] font-semibold text-[#B02A2A] border border-[#B02A2A] hover:bg-[#FBE8E8] rounded-[10px] transition-colors cursor-pointer">
                            Tolak / Minta Revisi LPJ
                        </button>
                        <button type="submit" onclick="document.getElementById('formLpjAction').value = 'APPROVE'"
                                class="px-4 py-2 text-[12.5px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-colors cursor-pointer">
                            Verifikasi & Sahkan LPJ
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        const lpjItems = {
            bagas: {
                id: 1,
                title: 'LPJ-2026/044 · Bagas Wicaksono',
                dest: 'Perjalanan Dinas ke Jakarta',
                pagu: 'Rp 3.780.000',
                real: 'Rp 3.655.000',
                diff: 'Rp 125.000',
                tiket: true,
                hotel: true,
                laporan: true,
                foto: false,
            },
            dewi: {
                id: 2,
                title: 'LPJ-2026/045 · Dewi Anggraini',
                dest: 'Perjalanan Dinas ke Surabaya',
                pagu: 'Rp 5.240.000',
                real: 'Rp 5.240.000',
                diff: 'Rp 0',
                tiket: true,
                hotel: true,
                laporan: true,
                foto: true,
            },
            nadia: {
                id: 3,
                title: 'LPJ-2026/046 · Nadia Maharani',
                dest: 'Perjalanan Dinas ke Bogor',
                pagu: 'Rp 1.520.000',
                real: 'Rp 1.520.000',
                diff: 'Rp 0',
                tiket: true,
                hotel: true,
                laporan: true,
                foto: true,
            }
        };

        function selectLpj(key) {
            document.querySelectorAll('.lpj-card').forEach(c => {
                c.className = 'lpj-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all';
            });
            const activeCard = document.getElementById('card-' + key);
            if (activeCard) {
                activeCard.className = 'lpj-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all';
            }

            const item = lpjItems[key];
            if (!item) return;

            document.getElementById('lpjTitle').innerText = item.title;
            document.getElementById('lpjDest').innerText = item.dest;
            document.getElementById('lpjPagu').innerText = item.pagu;
            document.getElementById('lpjReal').innerText = item.real;
            document.getElementById('lpjDiff').innerText = item.diff;
            document.getElementById('formLpjId').value = item.id;

            const makeBadge = (ok) => ok
                ? '<span class="inline-flex items-center font-medium rounded-full px-2.5 py-0.5 text-[11.5px] bg-[#DFF5F4] text-[#0B7A77]">Valid</span>'
                : '<span class="inline-flex items-center font-medium rounded-full px-2.5 py-0.5 text-[11.5px] bg-[#FFF1D1] text-[#8A5A00]">Kurang</span>';

            document.getElementById('fileTiket').innerHTML = makeBadge(item.tiket);
            document.getElementById('fileHotel').innerHTML = makeBadge(item.hotel);
            document.getElementById('fileLaporan').innerHTML = makeBadge(item.laporan);
            document.getElementById('fileFoto').innerHTML = makeBadge(item.foto);
        }
    </script>
</x-layouts.app>
