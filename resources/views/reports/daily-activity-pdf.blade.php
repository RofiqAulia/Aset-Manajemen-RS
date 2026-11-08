@php
    $logoPath = public_path('assets/img/logo-rs-ummi-png.png');
    $logoBase64 = $logoBase64 ?? (file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Log Aktivitas Harian - SIMAS RS UMMI</title>
    <style>
        @page {
            margin: 20px 25px;
            size: a4 landscape;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 10px;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .header-table td {
            border: none;
            padding: 0;
        }
        .hospital-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .hospital-sub {
            font-size: 11px;
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 3px;
        }
        .hospital-address {
            font-size: 9px;
            color: #64748b;
        }
        .divider-double {
            margin-top: 6px;
            margin-bottom: 12px;
            border-top: 2px solid #0f172a;
            border-bottom: 1px solid #94a3b8;
            height: 3px;
        }
        .report-title-box {
            text-align: center;
            margin-bottom: 12px;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9px;
        }
        .meta-info td {
            padding: 2px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-info {
            background-color: #e0f2fe;
            color: #075985;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
            border-collapse: collapse;
        }
        .signature-table td {
            border: none;
            vertical-align: top;
            font-size: 9.5px;
        }
        .signature-space {
            height: 55px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .sign-title {
            color: #64748b;
            font-size: 8.5px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 80px; vertical-align: middle; text-align: left;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo RS UMMI" style="height: 60px; max-width: 80px;">
                @endif
            </td>
            <td style="vertical-align: middle; text-align: center; padding-right: 80px;">
                <div class="hospital-title">Rumah Sakit UMMI Bogor</div>
                <div class="hospital-sub">Instalasi Pemeliharaan Sarana Rumah Sakit (IPSRS)</div>
                <div class="hospital-address">Jl. Empang No. 2, Bogor Selatan, Kota Bogor, Jawa Barat 16132 | Telp: (0251) 8341600 | Email: info@rsummi.com</div>
            </td>
        </tr>
    </table>
    <div class="divider-double"></div>

    <div class="report-title-box">
        <div class="report-title">Laporan Log Aktivitas Harian Pemeliharaan Aset</div>
        <div class="report-subtitle">Dokumen Resmi Pencatatan Inspeksi, Perawatan Preventif, dan Penanganan Aset Rumah Sakit</div>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;"><strong>Waktu Cetak:</strong></td>
            <td style="width: 35%;">{{ $printedAt ?? now()->translatedFormat('d F Y H:i:s') }}</td>
            <td style="width: 15%;"><strong>Total Aktivitas:</strong></td>
            <td style="width: 35%;">{{ count($logs) }} Kegiatan Tercatat</td>
        </tr>
        <tr>
            <td><strong>Status Filter:</strong></td>
            <td>
                @if(!empty($startDate) || !empty($endDate))
                    Periode: {{ $startDate ?? 'Awal' }} s.d. {{ $endDate ?? 'Sekarang' }}
                @else
                    Seluruh Periode Tercatat
                @endif
            </td>
            <td><strong>Departemen / Unit:</strong></td>
            <td>Instalasi Pemeliharaan Sarana Rumah Sakit (IPSRS)</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 12%;">Kode Aset</th>
                <th style="width: 18%;">Nama Aset & Lokasi</th>
                <th style="width: 14%;">Teknisi Pelaksana</th>
                <th style="width: 10%; text-align: center;">Status</th>
                <th style="width: 16%;">Temuan & Kondisi</th>
                <th style="width: 16%;">Tindakan Diambil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->performed_at)->translatedFormat('d M Y') }}</td>
                    <td><strong>{{ $log->asset->asset_code ?? '-' }}</strong></td>
                    <td>
                        <strong>{{ $log->asset->name ?? '-' }}</strong><br>
                        <span style="color: #64748b; font-size: 8.5px;">Lokasi: {{ $log->asset->location->name ?? 'Semua Lokasi' }}</span>
                    </td>
                    <td>{{ $log->technician->name ?? 'Tim IPSRS' }}</td>
                    <td style="text-align: center;">
                        @php
                            $statusVal = $log->status instanceof \App\Enums\MaintenanceLogStatus ? $log->status->value : (string)$log->status;
                            $badgeClass = match($statusVal) {
                                'selesai' => 'badge-success',
                                'dijadwalkan' => 'badge-info',
                                default => 'badge-danger',
                            };
                            $statusLabel = $log->status instanceof \App\Enums\MaintenanceLogStatus ? $log->status->getLabel() : ucfirst($statusVal);
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>{{ $log->findings ?? 'Pemeriksaan rutin sesuai SOP' }}</td>
                    <td>{{ $log->action_taken ?? 'Pengecekan fungsi dan kalibrasi operasional harian' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #64748b;">
                        Belum ada data log aktivitas harian yang tercatat pada sistem.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                Bogor, {{ now()->translatedFormat('d F Y') }}<br>
                Mengetahui,<br>
                <strong>Kepala Instalasi Pemeliharaan Sarana Rumah Sakit (IPSRS)</strong>
                <div class="signature-space"></div>
                <div class="sign-name">Ir. Triyono Prabowo, S.T.</div>
                <div class="sign-title">NIP. 19850920 201001 1 005</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        SIMAS-RS UMMI Bogor &bull; Dokumen Resmi IPSRS &bull; Dicetak secara otomatis oleh sistem
    </div>
</body>
</html>
