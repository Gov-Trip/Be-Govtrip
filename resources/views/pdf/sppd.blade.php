<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPPD - {{ $travelRequest->sppd_number ?? $travelRequest->id }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.5;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #111827;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #4b5563;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 14px;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-title span {
            font-size: 12px;
            font-weight: bold;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.info-table th,
        table.info-table td {
            border: 1px solid #374151;
            padding: 8px 10px;
            vertical-align: top;
            text-align: left;
        }
        table.info-table td.no {
            width: 30px;
            text-align: center;
        }
        table.info-table td.label {
            width: 35%;
            font-weight: bold;
            background-color: #f9fafb;
        }
        .section-title {
            font-weight: bold;
            margin: 15px 0 8px;
            font-size: 13px;
        }
        table.grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.grid-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            border: 1px solid #374151;
            padding: 7px;
            font-size: 11px;
            text-align: left;
        }
        table.grid-table td {
            border: 1px solid #374151;
            padding: 7px;
            font-size: 11px;
        }
        .signature-box {
            width: 100%;
            margin-top: 35px;
        }
        .signature-col {
            width: 45%;
            float: right;
            text-align: center;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>SISTEM INFORMASI PERJALANAN DINAS (GOV.TRIP)</h2>
        <p>Dokumen Resmi Surat Perintah Perjalanan Dinas Elektronik (e-SPPD)</p>
    </div>

    <div class="doc-title">
        <h3>SURAT PERINTAH PERJALANAN DINAS (SPPD)</h3>
        <span>Nomor: {{ $travelRequest->sppd_number ?? 'BELUM DITERBITKAN' }}</span>
    </div>

    <table class="info-table">
        <tr>
            <td class="no">1.</td>
            <td class="label">Pegawai yang Diperintahkan (Ketua)</td>
            <td>
                <strong>{{ $travelRequest->requester->name ?? '-' }}</strong><br>
                NIP: {{ $travelRequest->requester->nip ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="no">2.</td>
            <td class="label">Jabatan / Unit Kerja / Grade</td>
            <td>
                {{ $travelRequest->requester->position ?? '-' }} /
                {{ $travelRequest->requester->department->name ?? '-' }}
                ({{ $travelRequest->requester->grade ?? '-' }})
            </td>
        </tr>
        <tr>
            <td class="no">3.</td>
            <td class="label">Maksud Perjalanan Dinas</td>
            <td>{{ $travelRequest->purpose ?? '-' }}</td>
        </tr>
        <tr>
            <td class="no">4.</td>
            <td class="label">Kota Asal &amp; Kota Tujuan</td>
            <td>
                <strong>Asal:</strong> {{ $travelRequest->originCity->city_name ?? '-' }} ({{ $travelRequest->originCity->province_name ?? '-' }})<br>
                <strong>Tujuan:</strong> {{ $travelRequest->destinationCity->city_name ?? '-' }} ({{ $travelRequest->destinationCity->province_name ?? '-' }})
            </td>
        </tr>
        <tr>
            <td class="no">5.</td>
            <td class="label">Tanggal Berangkat &amp; Kembali</td>
            <td>
                {{ optional($travelRequest->start_date)->format('d M Y') ?? $travelRequest->start_date }}
                s/d
                {{ optional($travelRequest->end_date)->format('d M Y') ?? $travelRequest->end_date }}
            </td>
        </tr>
        <tr>
            <td class="no">6.</td>
            <td class="label">Transportasi &amp; Akomodasi</td>
            <td>
                <strong>Transportasi:</strong> {{ $travelRequest->logistics->transport_type ?? 'Belum Diinput' }}
                (Kode Booking: {{ $travelRequest->logistics->booking_code_or_ticket ?? '-' }})<br>
                <strong>Hotel / Akomodasi:</strong> {{ $travelRequest->logistics->hotel_name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="no">7.</td>
            <td class="label">Anggaran Terstandarisasi (SBM)</td>
            <td>
                <strong>Rp {{ number_format((float) ($travelRequest->advance_payment > 0 ? $travelRequest->advance_payment : $travelRequest->estimated_total_cost), 0, ',', '.') }}</strong>
                (Status: {{ $travelRequest->status }})
            </td>
        </tr>
    </table>

    @if($travelRequest->assignees && $travelRequest->assignees->count() > 0)
        <div class="section-title">Daftar Anggota Rombongan Dinas:</div>
        <table class="grid-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Pegawai</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Peran Tim</th>
                </tr>
            </thead>
            <tbody>
                @foreach($travelRequest->assignees as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->employee->name ?? '-' }}</td>
                        <td>{{ $item->employee->nip ?? '-' }}</td>
                        <td>{{ $item->employee->position ?? '-' }}</td>
                        <td>{{ $item->is_leader ? 'Ketua Rombongan' : 'Anggota' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="signature-box clearfix">
        <div class="signature-col">
            <p>
                Ditetapkan pada: {{ now()->format('d M Y') }}<br>
                <strong>Pejabat yang Menyetujui,</strong>
            </p>
            <br><br><br>
            @php
                $lastApproval = $travelRequest->approvals ? $travelRequest->approvals->last() : null;
            @endphp
            <p>
                <strong><u>{{ $lastApproval->approver->name ?? 'Atasan Langsung / Pejabat Berwenang' }}</u></strong><br>
                NIP: {{ $lastApproval->approver->nip ?? '-' }}
            </p>
        </div>
    </div>

</body>
</html>