<x-layouts.app title="Kotak Masuk Persetujuan - Gov.Trip" role="Atasan" headerTitle="Kotak Masuk Persetujuan">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: List Antrean (col-span-4) -->
        <div class="lg:col-span-4 flex flex-col gap-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-[13px] text-[#64748B] font-medium">Antrean Verifikasi (3)</span>
            </div>

            <div onclick="selectSubmission('rudi')" id="item-rudi" class="inbox-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Rudi &middot; Surabaya</span>
                    <x-badge status="Menunggu">Menunggu</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">PD-2026/093 &middot; 12–14 Okt 2026</span>
                <span class="text-[12.5px] font-semibold text-[#0066B3] block mt-1">Rp 6.120.000</span>
            </div>

            <div onclick="selectSubmission('sari')" id="item-sari" class="inbox-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Sari &middot; Yogyakarta</span>
                    <x-badge status="Menunggu">Menunggu</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">PD-2026/094 &middot; 15–17 Okt 2026</span>
                <span class="text-[12.5px] font-semibold text-[#0066B3] block mt-1">Rp 3.500.000</span>
            </div>

            <div onclick="selectSubmission('bagas')" id="item-bagas" class="inbox-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Bagas &middot; Jakarta</span>
                    <x-badge status="Menunggu">Menunggu</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">PD-2026/095 &middot; 24–26 Okt 2026</span>
                <span class="text-[12.5px] font-semibold text-[#0066B3] block mt-1">Rp 4.250.000</span>
            </div>
        </div>

        <!-- Right: Detail Panel (col-span-8) -->
        <div class="lg:col-span-8">
            <x-card padding="lg">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E8EEF3] gap-2 mb-5">
                    <div>
                        <h3 id="detailNumber" class="text-[17px] font-bold text-[#123B72]">PD-2026/093 &middot; Rudi</h3>
                        <p id="detailUnit" class="text-[12.5px] text-[#64748B]">Bidang Perencanaan & Program</p>
                    </div>
                    <x-badge status="Menunggu">Menunggu Persetujuan</x-badge>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 text-[13px]">
                    <div>
                        <span class="text-[#64748B] block text-[12px]">Kota Tujuan</span>
                        <span id="detailDest" class="font-semibold text-[#1E293B]">Surabaya (Jawa Timur)</span>
                    </div>
                    <div>
                        <span class="text-[#64748B] block text-[12px]">Tanggal Pelaksanaan</span>
                        <span id="detailDates" class="font-semibold text-[#1E293B]">12–14 Okt 2026</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-[#64748B] block text-[12px]">Maksud Perjalanan</span>
                        <p id="detailPurpose" class="font-medium text-[#1E293B] mt-0.5 leading-relaxed">
                            Monitoring implementasi program daerah bersama perwakilan dinas provinsi Jawa Timur.
                        </p>
                    </div>
                </div>

                <!-- Cost Breakdown Table -->
                <div class="py-4 border-t border-[#E8EEF3]">
                    <span class="text-[13px] font-semibold text-[#123B72] block mb-2">Rincian Anggaran Diajukan</span>
                    <div class="w-full overflow-x-auto">
                        <table class="w-full text-[13px] text-left border-collapse">
                            <thead class="bg-slate-50 border-y border-[#E8EEF3]">
                                <tr>
                                    <th class="py-2 px-3 text-[#64748B] text-[12px] font-medium">Uraian</th>
                                    <th class="py-2 px-3 text-[#64748B] text-[12px] font-medium text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody id="detailCostBody" class="divide-y divide-[#E8EEF3]">
                                <tr><td class="py-2 px-3 text-[#1E293B]">Transportasi Tiket Pesawat PP</td><td class="py-2 px-3 text-right font-medium text-[#1E293B]">Rp 2.400.000</td></tr>
                                <tr><td class="py-2 px-3 text-[#1E293B]">Akomodasi Hotel (2 Malam)</td><td class="py-2 px-3 text-right font-medium text-[#1E293B]">Rp 2.100.000</td></tr>
                                <tr><td class="py-2 px-3 text-[#1E293B]">Uang Harian SBM 2026</td><td class="py-2 px-3 text-right font-medium text-[#1E293B]">Rp 1.500.000</td></tr>
                                <tr><td class="py-2 px-3 text-[#1E293B]">Biaya Lain-lain / Taksi Bandara</td><td class="py-2 px-3 text-right font-medium text-[#1E293B]">Rp 120.000</td></tr>
                            </tbody>
                            <tfoot class="border-t border-[#E8EEF3] bg-slate-50 font-bold">
                                <tr>
                                    <td class="py-2 px-3 text-[#123B72]">Total Estimasi Biaya</td>
                                    <td id="detailTotal" class="py-2 px-3 text-right text-[#0066B3]">Rp 6.120.000</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Decision Form -->
                <form action="{{ route('atasan.decision.post') }}" method="POST" class="pt-4 border-t border-[#E8EEF3] flex flex-col gap-3">
                    @csrf
                    <input type="hidden" name="submission_id" id="formSubId" value="1" />
                    <input type="hidden" name="decision" id="formDecision" value="Disetujui" />

                    <div>
                        <label class="text-[#64748B] text-[12px] font-medium block mb-1">Catatan Keputusan (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Tulis catatan persetujuan atau alasan revisi..."
                                  class="w-full px-3 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">Disetujui. Koordinasikan hasil pertemuan dengan tim perencanaan setelah kembali.</textarea>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2">
                        <button type="submit" onclick="document.getElementById('formDecision').value = 'Ditolak'"
                                class="px-3.5 py-2 text-[12.5px] font-semibold text-[#B02A2A] border border-[#B02A2A] hover:bg-[#FBE8E8] rounded-[10px] transition-colors cursor-pointer">
                            Tolak Pengajuan
                        </button>
                        <button type="submit" onclick="document.getElementById('formDecision').value = 'Perlu Revisi'"
                                class="px-3.5 py-2 text-[12.5px] font-semibold text-[#8A5A00] bg-[#FFF1D1] hover:bg-[#fee7b3] rounded-[10px] transition-colors cursor-pointer">
                            Minta Revisi
                        </button>
                        <button type="submit" onclick="document.getElementById('formDecision').value = 'Disetujui'"
                                class="px-4 py-2 text-[12.5px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-colors cursor-pointer">
                            Setujui Pengajuan
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        const submissions = {
            rudi: {
                id: 1,
                number: 'PD-2026/093 · Rudi',
                unit: 'Bidang Perencanaan & Program',
                dest: 'Surabaya (Jawa Timur)',
                dates: '12–14 Okt 2026',
                purpose: 'Monitoring implementasi program daerah bersama perwakilan dinas provinsi Jawa Timur.',
                costs: [
                    ['Transportasi Tiket Pesawat PP', 'Rp 2.400.000'],
                    ['Akomodasi Hotel (2 Malam)', 'Rp 2.100.000'],
                    ['Uang Harian SBM 2026', 'Rp 1.500.000'],
                    ['Biaya Lain-lain / Taksi Bandara', 'Rp 120.000'],
                ],
                total: 'Rp 6.120.000'
            },
            sari: {
                id: 2,
                number: 'PD-2026/094 · Sari',
                unit: 'Bidang Perencanaan & Program',
                dest: 'Yogyakarta (DI Yogyakarta)',
                dates: '15–17 Okt 2026',
                purpose: 'Bimbingan teknis penyusunan rencana aksi pembangunan berkelanjutan.',
                costs: [
                    ['Transportasi Kereta Eksekutif PP', 'Rp 900.000'],
                    ['Akomodasi Hotel (2 Malam)', 'Rp 1.600.000'],
                    ['Uang Harian SBM 2026', 'Rp 1.000.000'],
                ],
                total: 'Rp 3.500.000'
            },
            bagas: {
                id: 3,
                number: 'PD-2026/095 · Bagas',
                unit: 'Bidang Perencanaan & Program',
                dest: 'Jakarta Pusat (DKI Jakarta)',
                dates: '24–26 Okt 2026',
                purpose: 'Koordinasi data indikator sasaran pembangunan daerah bersama kementerian.',
                costs: [
                    ['Transportasi Kereta Cepat / Argo PP', 'Rp 1.250.000'],
                    ['Akomodasi Hotel', 'Rp 1.800.000'],
                    ['Uang Harian', 'Rp 1.200.000'],
                ],
                total: 'Rp 4.250.000'
            }
        };

        function selectSubmission(key) {
            document.querySelectorAll('.inbox-card').forEach(c => {
                c.className = 'inbox-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all';
            });
            const activeCard = document.getElementById('item-' + key);
            if (activeCard) {
                activeCard.className = 'inbox-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all';
            }

            const data = submissions[key];
            if (!data) return;

            document.getElementById('detailNumber').innerText = data.number;
            document.getElementById('detailUnit').innerText = data.unit;
            document.getElementById('detailDest').innerText = data.dest;
            document.getElementById('detailDates').innerText = data.dates;
            document.getElementById('detailPurpose').innerText = data.purpose;
            document.getElementById('detailTotal').innerText = data.total;
            document.getElementById('formSubId').value = data.id;

            let html = '';
            data.costs.forEach(c => {
                html += `<tr><td class="py-2 px-3 text-[#1E293B]">${c[0]}</td><td class="py-2 px-3 text-right font-medium text-[#1E293B]">${c[1]}</td></tr>`;
            });
            document.getElementById('detailCostBody').innerHTML = html;
        }
    </script>
</x-layouts.app>
