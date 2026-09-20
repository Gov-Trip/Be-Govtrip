<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ Final - {{ $travelRequest->sppd_number ?? $travelRequest->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2, .header h3, .header h4 { margin: 0; padding: 2px 0; text-transform: uppercase; }
        
        .section-title { font-size: 13px; font-weight: bold; background-color: #f0f0f0; padding: 5px; margin-top: 20px; border: 1px solid #000; }
        
        /* Tabel untuk Data Layout */
        .layout-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .layout-table td { padding: 5px; vertical-align: top; }
        .layout-table td.label { width: 30%; font-weight: bold; }
        .layout-table td.separator { width: 2%; text-align: center; }
        
        /* Tabel untuk List Data (Riwayat/Logistik) */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; text-align: left; }
        .data-table th { background-color: #e4e4e4; font-weight: bold; text-align: center; }
        
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .fw-bold { font-weight: bold; }
        
        .footer-ttd { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .footer-ttd td { text-align: center; vertical-align: bottom; height: 100px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>KEMENTERIAN / LEMBAGA NEGARA</h2>
        <h3>LAPORAN PERTANGGUNGJAWABAN (LPJ) PERJALANAN DINAS</h3>
        <h4>GOV.TRIP SYSTEM</h4>
        <p style="margin-top: 10px;"><strong>No. Dokumen:</strong> {{ $travelRequest->sppd_number ?? strtoupper(substr($travelRequest->id, 0, 8)) }}</p>
    </div>

    <div class="section-title">A. INFORMASI PEMOHON & PERJALANAN</div>
    <table class="layout-table">
        <tr>
            <td class="label">Nama Pegawai</td>
            <td class="separator">:</td>
            <td>{{ $travelRequest->requester->name ?? '-' }} (NIP: {{ $travelRequest->requester->nip ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label">Departemen / Jabatan</td>
            <td class="separator">:</td>
            <td>{{ $travelRequest->requester->department->name ?? '-' }} / {{ $travelRequest->requester->position ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Rute Perjalanan</td>
            <td class="separator">:</td>
            <td>{{ $travelRequest->originCity->city_name ?? '-' }} &rarr; {{ $travelRequest->destinationCity->city_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Pelaksanaan</td>
            <td class="separator">:</td>
            <td>
                {{ $travelRequest->start_date ? \Carbon\Carbon::parse($travelRequest->start_date)->format('d M Y') : '-' }} 
                s/d 
                {{ $travelRequest->end_date ? \Carbon\Carbon::parse($travelRequest->end_date)->format('d M Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Maksud / Tujuan</td>
            <td class="separator">:</td>
            <td>{{ $travelRequest->purpose ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">B. LOGISTIK & AKOMODASI (Disediakan Admin Travel)</div>
    @if($travelRequest->logistics)
    <table class="data-table">
        <tr>
            <th>Jenis Transportasi</th>
            <th>Kode Booking / Tiket</th>
            <th>Nama Hotel / Penginapan</th>
            <th>Waktu Keberangkatan</th>
        </tr>
        <tr>
            <td class="text-center">{{ $travelRequest->logistics->transport_type ?? '-' }}</td>
            <td class="text-center">{{ $travelRequest->logistics->booking_code_or_ticket ?? '-' }}</td>
            <td class="text-center">{{ $travelRequest->logistics->hotel_name ?? '-' }}</td>
            <td class="text-center">{{ $travelRequest->logistics->departure_datetime ? \Carbon\Carbon::parse($travelRequest->logistics->departure_datetime)->format('d M Y H:i') : '-' }}</td>
        </tr>
    </table>
    @else
    <p style="padding-left: 5px;"><em>Tidak ada catatan logistik / tiket untuk perjalanan dinas ini.</em></p>
    @endif

    <div class="section-title">C. RIWAYAT PERSETUJUAN (Approval Trail)</div>
    <table class="data-table">
        <tr>
            <th>Tahap</th>
            <th>Nama Penyetuju</th>
            <th>Waktu Keputusan</th>
            <th>Catatan (Notes)</th>
        </tr>
        <!-- Loop riwayat approval atasan (Diperbaiki agar kompatibel dengan DOMPDF) -->
        @if($travelRequest->approvals && $travelRequest->approvals->count() > 0)
            @foreach($travelRequest->approvals as $approval)
            <tr>
                <td>Persetujuan Berjenjang</td>
                <td>{{ $approval->approver->name ?? 'Sistem' }}</td>
                <td class="text-center">{{ $approval->created_at ? $approval->created_at->format('d M Y H:i') : '-' }}</td>
                <td>{{ $approval->notes ?? 'Disetujui' }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4" class="text-center"><em>Riwayat persetujuan tidak ditemukan.</em></td>
            </tr>
        @endif
    </table>

    <div class="section-title">D. RINCIAN KEUANGAN (Verifikasi Bendahara)</div>
    <table class="layout-table">
        <tr>
            <td class="label">Dana Cair (Advance Payment)</td>
            <td class="separator">:</td>
            <td class="fw-bold">Rp {{ number_format($advancePayment, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Pengeluaran Karyawan</td>
            <td class="separator">:</td>
            <td>Rp {{ number_format($realExpense, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Status Saldo (Balance)</td>
            <td class="separator">:</td>
            <td>
                @if($balance > 0)
                    <strong>Sisa Rp {{ number_format($balance, 0, ',', '.') }}</strong> <em>(Wajib disetor kembali ke kas negara/perusahaan)</em>
                @elseif($balance < 0)
                    <strong>Kurang Rp {{ number_format(abs($balance), 0, ',', '.') }}</strong> <em>(Akan direimburse ke karyawan)</em>
                @else
                    <strong>Nihil (Rp 0)</strong>
                @endif
            </td>
        </tr>
    </table>

    <!-- Bagian Tanda Tangan -->
    <table class="footer-ttd">
        <tr>
            <td width="33%">
                Yang Melaksanakan,
                <br><br><br><br><br>
                <strong><u>{{ $travelRequest->requester->name ?? '________________' }}</u></strong><br>
                NIP. {{ $travelRequest->requester->nip ?? '-' }}
            </td>
            <td width="34%">
                Mengetahui,<br>
                Admin Logistik
                <br><br><br><br><br>
                <strong><u>Bagian Logistik & Akomodasi</u></strong>
            </td>
            <td width="33%">
                Telah Diverifikasi,<br>
                Bendahara / Keuangan
                <br><br><br><br><br>
                <strong><u>Bagian Finance</u></strong>
            </td>
        </tr>
    </table>

</body>
</html>