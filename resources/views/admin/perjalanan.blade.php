<x-layouts.app title="Data Perjalanan & Logistik - Gov.Trip" role="Admin" headerTitle="Data Perjalanan & Tiket">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: List Perjalanan (col-span-5) -->
        <div class="lg:col-span-5 flex flex-col gap-3">
            <span class="text-[13px] text-[#64748B] font-medium px-1">Perjalanan yang Perlu Input Tiket / SPPD</span>

            <div onclick="selectAdminTrip('fajar')" id="admin-card-fajar" class="admin-trip-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Fajar &middot; Balikpapan</span>
                    <x-badge status="Menunggu">Perlu Tiket</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">PD-2026/090 &middot; 2–4 Nov 2026</span>
                <span class="text-[12px] text-[#0066B3] font-medium block mt-1">Disetujui Kadis &middot; Siap Terbit SPPD</span>
            </div>

            <div onclick="selectAdminTrip('liyana')" id="admin-card-liyana" class="admin-trip-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Liyana &middot; Yogyakarta</span>
                    <x-badge status="Berjalan">Berjalan</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">PD-2026/087 &middot; 21–23 Sep 2026</span>
                <span class="text-[12px] text-[#0B7A77] font-medium block mt-1">SPPD 090/087/SPPD/IX/2026</span>
            </div>
        </div>

        <!-- Right: Input Tiket & Terbitkan SPPD (col-span-7) -->
        <div class="lg:col-span-7">
            <x-card padding="lg">
                <div class="pb-4 border-b border-[#E8EEF3] mb-5">
                    <h3 id="adminTripTitle" class="text-[17px] font-bold text-[#123B72]">PD-2026/090 &middot; Fajar (Balikpapan)</h3>
                    <p class="text-[12.5px] text-[#64748B] mt-0.5">Input nomor tiket transportasi dan penginapan resmi</p>
                </div>

                <form action="{{ route('admin.logistics.store') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="trip_id" id="adminTripId" value="1" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Jenis Tiket / Maskapai</label>
                            <input type="text" name="transport_type" value="Garuda Indonesia (GA-512)" required
                                   class="px-3.5 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Kode Booking / Tiket</label>
                            <input type="text" name="booking_code" value="GA-7890214" required
                                   class="px-3.5 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Akomodasi Hotel</label>
                            <input type="text" name="hotel_name" value="Grand Senyiur Balikpapan (Room 402)" required
                                   class="px-3.5 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Jadwal Keberangkatan</label>
                            <input type="datetime-local" name="departure_datetime" value="2026-11-02T07:30" required
                                   class="px-3.5 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        </div>
                    </div>

                    <div class="p-3.5 rounded-[10px] bg-[#E3F0FA] text-[#0066B3] text-[12.5px] flex items-center justify-between">
                        <span>SPPD Digital dapat otomatis digenerate dan ditandatangani secara elektronik (TTE).</span>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E8EEF3]">
                        <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-all cursor-pointer">
                            Simpan Tiket & Terbitkan SPPD Digital
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        function selectAdminTrip(key) {
            document.querySelectorAll('.admin-trip-card').forEach(c => {
                c.className = 'admin-trip-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all';
            });
            const active = document.getElementById('admin-card-' + key);
            if (active) {
                active.className = 'admin-trip-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all';
            }
        }
    </script>
</x-layouts.app>
