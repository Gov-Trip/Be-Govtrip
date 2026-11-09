<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPPD - {{ $travelRequest->sppd_number ?? $travelRequest->id }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 12pt;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            font-style: italic;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0;
        }
        .doc-title h3 {
            margin: 0;
            text-decoration: underline;
            font-size: 13pt;
            font-weight: bold;
        }
        .doc-title p {
            margin: 4px 0 0 0;
            font-size: 11pt;
        }
        table.sppd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.sppd-table th, table.sppd-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 10.5pt;
        }
        table.sppd-table td.num {
            width: 5%;
            text-align: center;
        }
        table.sppd-table td.col-label {
            width: 42%;
        }
        table.sppd-table td.col-value {
            width: 53%;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-table td {
            vertical-align: top;
            font-size: 10.5pt;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>PEMERINTAH REPUBLIK INDONESIA</h2>
        <h3>SISTEM PERJALANAN DINAS GOV.TRIP</h3>
        <p>Jalan Medan Merdeka Barat No. 8, Jakarta Pusat · Portal Resmi SPPD Digital</p>
    </div>

    <div class="doc-title">
        <h3>SURAT PERINTAH PERJALANAN DINAS (SPPD)</h3>
        <p>Nomor: <strong>{{ $travelRequest->sppd_number ?? '090/SPPD/'.date('Y') }}</strong></p>
    </div>

    <table class="sppd-table">
        <tr>
            <td class="num">1.</td>
            <td class="col-label">Pejabat Pembuat Komitmen / Yang Memberi Perintah</td>
            <td class="col-value">Kepala Badan Kepegawaian & Perencanaan Daerah</td>
        </tr>
        <tr>
            <td class="num">2.</td>
            <td class="col-label">Nama / NIP Pegawai yang diperintahkan</td>
            <td class="col-value">
                <strong>{{ $travelRequest->requester->name ?? '-' }}</strong><br>
                NIP. {{ $travelRequest->requester->nip ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="num">3.</td>
            <td class="col-label">
                a. Pangkat dan Golongan<br>
                b. Jabatan / Instansi<br>
                c. Tingkat Biaya Perjalanan Dinas
            </td>
            <td class="col-value">
                a. {{ $travelRequest->requester->grade ?? 'Penata Muda / III-a' }}<br>
                b. {{ $travelRequest->requester->position ?? '-' }} / {{ $travelRequest->requester->department->name ?? '-' }}<br>
                c. Standar Biaya Masukan (SBM)
            </td>
        </tr>
        <tr>
            <td class="num">4.</td>
            <td class="col-label">Maksud Perjalanan Dinas</td>
            <td class="col-value">{{ $travelRequest->purpose ?? '-' }}</td>
        </tr>
        <tr>
            <td class="num">5.</td>
            <td class="col-label">Alat Angkut yang dipergunakan</td>
            <td class="col-value">{{ $travelRequest->logistics->transport_type ?? 'Kereta Api / Pesawat Udara / Transportasi Dinas' }}</td>
        </tr>
        <tr>
            <td class="num">6.</td>
            <td class="col-label">
                a. Tempat Berangkat<br>
                b. Tempat Tujuan
            </td>
            <td class="col-value">
                a. {{ $travelRequest->originCity->city_name ?? 'Jakarta Pusat' }}<br>
                b. {{ $travelRequest->destinationCity->city_name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="num">7.</td>
            <td class="col-label">
                a. Lamanya Perjalanan Dinas<br>
                b. Tanggal Berangkat<br>
                c. Tanggal Harus Kembali
            </td>
            <td class="col-value">
                @php
                    $start = \Carbon\Carbon::parse($travelRequest->start_date);
                    $end = \Carbon\Carbon::parse($travelRequest->end_date);
                    $days = $start->diffInDays($end) + 1;
                @endphp
                a. {{ $days }} ({{ $days == 1 ? 'Satu' : ($days == 2 ? 'Dua' : ($days == 3 ? 'Tiga' : $days)) }}) hari<br>
                b. {{ $start->format('d F Y') }}<br>
                c. {{ $end->format('d F Y') }}
            </td>
        </tr>
        <tr>
            <td class="num">8.</td>
            <td class="col-label">Pengikut / Rombongan</td>
            <td class="col-value">
                @if($travelRequest->assignees && count($travelRequest->assignees) > 0)
                    <ol style="margin: 0; padding-left: 18px;">
                        @foreach($travelRequest->assignees as $assignee)
                            <li>{{ $assignee->employee->name ?? '-' }} ({{ $assignee->employee->nip ?? '-' }})</li>
                        @endforeach
                    </ol>
                @else
                    - (Nihil)
                @endif
            </td>
        </tr>
        <tr>
            <td class="num">9.</td>
            <td class="col-label">
                Pembebanan Anggaran<br>
                a. Instansi / Mata Anggaran<br>
                b. Estimasi Plafon Biaya
            </td>
            <td class="col-value">
                a. DPA-SKPD Tahun Anggaran {{ date('Y') }}<br>
                b. Rp {{ number_format($travelRequest->estimated_total_cost ?? 0, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td class="num">10.</td>
            <td class="col-label">Keterangan Lain-Lain</td>
            <td class="col-value">
                Surat perintah ini diterbitkan secara sah dan tersimpan secara digital pada basis data Gov.Trip.
            </td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">
                Dikeluarkan di: Jakarta Pusat<br>
                Pada tanggal: {{ \Carbon\Carbon::parse($travelRequest->created_at)->format('d F Y') }}<br><br>
                <strong>Pejabat Pembuat Komitmen,</strong>
                <br><br><br><br><br>
                <u><strong>Drs. H. Mulyono, M.Si.</strong></u><br>
                NIP. 19740512 199803 1 002
            </td>
        </tr>
    </table>

</body>
</html>

