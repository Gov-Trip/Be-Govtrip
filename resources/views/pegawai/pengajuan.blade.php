<x-layouts.app title="{{ isset($travel) ? 'Edit Pengajuan - Gov.Trip' : 'Pengajuan Perjalanan Dinas - Gov.Trip' }}" role="Pegawai" headerTitle="Pengajuan Perjalanan Dinas">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Form Input (col-span-8) -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <x-card>
                <div class="border-b border-[#E8EEF3] pb-4 mb-6">
                    <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">
                        {{ isset($travel) ? 'Perbaiki / Edit Pengajuan Perjalanan Dinas' : 'Formulir Pengajuan Baru' }}
                    </h3>
                    <p class="text-[#64748B] text-[12.5px] mt-0.5">
                        {{ isset($travel) ? 'Perbarui data sesuai catatan perbaikan atau revisi dari Atasan / Admin Travel' : 'Lengkapi informasi rencana perjalanan dinas Anda untuk diteruskan ke atasan' }}
                    </p>
                </div>

                @if(session('success'))
                    <div class="mb-5 p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-4 rounded-xl bg-[#FDE8E8] text-[#9B1C1C] border border-[#f8b4b4] text-[13px] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(isset($travel) && $travel->rejection_reason)
                    <div class="mb-5 p-4 rounded-xl bg-[#FFF1D1] text-[#8A5A00] border border-[#ffe099] text-[13px] flex items-start gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mt-0.5 shrink-0"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div>
                            <span class="font-bold block">Catatan Perbaikan / Revisi:</span>
                            <p class="mt-0.5 leading-relaxed">{{ $travel->rejection_reason }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ isset($travel) ? route('pegawai.pengajuan.update', $travel->id) : route('pegawai.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                    @csrf
                    @if(isset($travel))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Tipe Perjalanan</label>
                            <select name="trip_type" class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">
                                <option value="Luar kota" selected>Luar Kota</option>
                                <option value="Dalam kota">Dalam Kota</option>
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Kota Tujuan</label>
                            <select name="destination_city_id" id="destCity" onchange="recalcBudget()" class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">
                                @foreach($cities ?? [] as $city)
                                    <option value="{{ $city->id }}" {{ (isset($travel) && $travel->destination_city_id == $city->id) ? 'selected' : ($city->city_name === 'Yogyakarta' ? 'selected' : '') }}>
                                        {{ $city->city_name }} ({{ $city->province_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Tanggal Berangkat</label>
                            <input type="date" name="start_date" id="startDate" onchange="recalcBudget()"
                                   value="{{ isset($travel) ? $travel->start_date?->format('Y-m-d') : date('Y-m-d', strtotime('+3 days')) }}" required
                                   class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Tanggal Kembali</label>
                            <input type="date" name="end_date" id="endDate" onchange="recalcBudget()"
                                   value="{{ isset($travel) ? $travel->end_date?->format('Y-m-d') : date('Y-m-d', strtotime('+5 days')) }}" required
                                   class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        </div>
                    </div>

                    <!-- Anggota Rombongan (Multi-assignees) -->
                    <div class="flex flex-col pt-1">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Anggota Rombongan (Opsional / Tim Tambahan)</label>
                        <div class="p-3 border border-[#E8EEF3] rounded-[10px] bg-slate-50/50 max-h-44 overflow-y-auto space-y-2 text-[12.5px]">
                            @php
                                $assignedIds = isset($travel) ? $travel->assignees->pluck('employee_id')->toArray() : [];
                            @endphp
                            @foreach($employees ?? [] as $emp)
                                <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-[#E8EEF3] hover:border-[#0066B3] cursor-pointer transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" name="assignee_ids[]" value="{{ $emp->id }}"
                                               {{ in_array($emp->id, $assignedIds) ? 'checked' : '' }}
                                               onchange="recalcBudget()"
                                               class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3] assignee-checkbox" />
                                        <div>
                                            <span class="font-semibold text-[#1E293B] block">{{ $emp->name }}</span>
                                            <span class="text-[11.5px] text-[#64748B]">{{ $emp->position }} &middot; {{ $emp->department?->name ?? 'Dinas' }}</span>
                                        </div>
                                    </div>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-[#E3F0FA] text-[#0066B3] font-medium">{{ $emp->grade ?? 'Staff' }}</span>
                                </label>
                            @endforeach
                        </div>
                        <span class="text-[11px] text-[#64748B] mt-1">* Pemohon utama otomatis terdaftar sebagai ketua rombongan.</span>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Maksud / Keperluan Perjalanan Dinas</label>
                        <textarea name="purpose" rows="3" required placeholder="Jelaskan secara rinci tujuan dan agenda dinas..."
                                  class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">{{ isset($travel) ? $travel->purpose : 'Rapat koordinasi penyusunan program regional bersama kementerian dan dinas terkait.' }}</textarea>
                    </div>

                    <!-- Upload Dokumen Pendukung (Surat Undangan / Nota Dinas / Telaahan Staf) -->
                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5 flex items-center justify-between">
                            <span>Dokumen Pendukung / Surat Undangan / Nota Dinas</span>
                            @if(isset($travel) && $travel->attachments && $travel->attachments->isNotEmpty())
                                <span class="text-[#0066B3] font-semibold text-[11.5px]">Dokumen Terunggah: {{ basename($travel->attachments->first()->file_path) }}</span>
                            @endif
                        </label>
                        <input type="file" name="attachment_document" id="attachmentDocument" accept=".pdf,.png,.jpg,.jpeg"
                               class="w-full px-3.5 py-2 text-[13px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3] file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[12px] file:font-semibold file:bg-[#E3F0FA] file:text-[#0066B3] hover:file:bg-[#d0e5f5]" />
                        <span class="text-[11px] text-[#64748B] mt-1">* Format PDF, JPG, atau PNG (maks 5MB) sebagai dokumen dasar permohonan dinas.</span>
                    </div>

                    <!-- Estimasi Biaya Grid -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-[#123B72] text-[13.5px] font-semibold">Estimasi Rincian Anggaran (Kalkulasi Otomatis SBM 2026)</label>
                            <span class="text-[11.5px] text-[#0066B3] font-medium">Berdasarkan Plafon Master SBM</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="flex flex-col">
                                <span class="text-[12px] text-[#64748B] mb-1">Plafon Transportasi</span>
                                <input type="number" id="costTrans" name="cost_transport" value="900000" readonly
                                       class="px-3 py-2 text-[13px] bg-slate-50 border border-[#E8EEF3] rounded-[8px] text-[#1E293B] font-medium" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[12px] text-[#64748B] mb-1">Plafon Hotel / Malam</span>
                                <input type="number" id="costHotel" name="cost_accommodation" value="1300000" readonly
                                       class="px-3 py-2 text-[13px] bg-slate-50 border border-[#E8EEF3] rounded-[8px] text-[#1E293B] font-medium" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[12px] text-[#64748B] mb-1">Uang Harian SBM</span>
                                <input type="number" id="costDaily" name="cost_daily" value="1140000" readonly
                                       class="px-3 py-2 text-[13px] bg-slate-50 border border-[#E8EEF3] rounded-[8px] text-[#1E293B] font-medium" />
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E8EEF3] mt-2">
                        <button type="submit" name="action_type" value="draft"
                                class="px-4 py-2.5 text-[13px] font-medium rounded-[10px] text-[#1E293B] border border-[#E8EEF3] hover:bg-[#F1F7FB] transition-all cursor-pointer">
                            Simpan Draft
                        </button>
                        <button type="submit" name="action_type" value="submit"
                                class="px-5 py-2.5 text-[13px] font-semibold rounded-[10px] bg-[#0066B3] hover:bg-[#005596] text-white shadow-sm transition-all cursor-pointer">
                            {{ isset($travel) && in_array($travel->status, ['Direvisi', 'Revisi']) ? 'Simpan & Ajukan Ulang' : 'Kirim Pengajuan' }}
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
                    <p class="text-[#64748B] text-[12px] mt-0.5">Sesuai alokasi pagu DPA dinas & Standar SBM</p>
                </div>

                <div class="flex flex-col gap-2.5 text-[13px]">
                    <div class="flex justify-between text-[#64748B]">
                        <span>Tiket Transportasi</span>
                        <span id="labelTrans" class="font-medium text-[#1E293B]">Rp 900.000</span>
                    </div>
                    <div class="flex justify-between text-[#64748B]">
                        <span>Akomodasi Hotel</span>
                        <span id="labelHotel" class="font-medium text-[#1E293B]">Rp 1.300.000</span>
                    </div>
                    <div class="flex justify-between text-[#64748B]">
                        <span>Uang Harian</span>
                        <span id="labelDaily" class="font-medium text-[#1E293B]">Rp 1.140.000</span>
                    </div>
                    <div class="pt-3 border-t border-[#E8EEF3] flex justify-between items-center">
                        <span class="font-semibold text-[#123B72]">Total Estimasi</span>
                        <span id="labelTotal" class="font-bold text-[18px] text-[#0066B3]">Rp 3.340.000</span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="border-b border-[#E8EEF3] pb-3 mb-4">
                    <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Alur Verifikasi Standar</h3>
                    <p class="text-[#64748B] text-[12px] mt-0.5">Sesuai Matriks Hak Akses & SOP Dinas</p>
                </div>

                @php
                    $steps = [
                        ['label' => 'Buat Pengajuan', 'sublabel' => 'Kota & Rombongan', 'status' => 'completed'],
                        ['label' => 'Upload Dokumen', 'sublabel' => 'Undangan / Nota Dinas', 'status' => 'current'],
                        ['label' => 'Approval Atasan', 'sublabel' => 'Kepala Seksi', 'status' => 'upcoming'],
                        ['label' => 'Approval Kabid', 'sublabel' => 'Kepala Bidang', 'status' => 'upcoming'],
                        ['label' => 'Approval Kadis', 'sublabel' => 'Kepala Dinas & SPPD', 'status' => 'upcoming'],
                        ['label' => 'Verifikasi Bendahara', 'sublabel' => 'Verifikasi LPJ', 'status' => 'upcoming'],
                    ];
                @endphp
                <x-stepper :steps="$steps" orientation="vertical" />
            </x-card>
        </div>
    </div>

    <script>
        const policyRates = {
            @foreach($policies ?? [] as $pol)
                "{{ $pol->destination_city_id }}_{{ $pol->grade_group }}": {
                    daily: {{ (int)$pol->daily_allowance }},
                    hotel: {{ (int)$pol->max_hotel_rate }},
                    trans: {{ (int)$pol->max_transport_rate }}
                },
            @endforeach
        };

        function recalcBudget() {
            const startVal = document.getElementById('startDate').value;
            const endVal = document.getElementById('endDate').value;
            const cityId = document.getElementById('destCity').value;

            let days = 3;
            if (startVal && endVal) {
                const s = new Date(startVal);
                const e = new Date(endVal);
                const diffTime = Math.abs(e - s);
                days = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1);
            }
            const nights = Math.max(0, days - 1);

            // Default rate fallback (Staff)
            const key = cityId + '_Staff';
            const rate = policyRates[key] || { daily: 380000, hotel: 650000, trans: 600000 };

            // Hitung rombongan
            const checkedBoxes = document.querySelectorAll('.assignee-checkbox:checked').length;
            const totalPersons = Math.max(1, 1 + checkedBoxes);

            const trans = rate.trans * totalPersons;
            const hotel = rate.hotel * nights * totalPersons;
            const daily = rate.daily * days * totalPersons;
            const total = trans + hotel + daily;

            const fmt = (n) => 'Rp ' + n.toLocaleString('id-ID');
            document.getElementById('costTrans').value = trans;
            document.getElementById('costHotel').value = hotel;
            document.getElementById('costDaily').value = daily;

            document.getElementById('labelTrans').innerText = fmt(trans);
            document.getElementById('labelHotel').innerText = fmt(hotel);
            document.getElementById('labelDaily').innerText = fmt(daily);
            document.getElementById('labelTotal').innerText = fmt(total);
        }

        document.addEventListener('DOMContentLoaded', recalcBudget);
    </script>
</x-layouts.app>
