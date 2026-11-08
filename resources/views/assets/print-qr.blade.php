<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cetak Label QR Barang' }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #000000;
            padding: 20px;
        }

        /* Top Action Bar (Screen Only) */
        .toolbar {
            max-width: 980px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .toolbar h1 {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .toolbar .actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease-in-out;
        }

        .btn-primary {
            background-color: #0284c7;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #0369a1;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #475569;
            border-color: #cbd5e1;
        }

        .btn-secondary:hover {
            background-color: #f8fafc;
        }

        /* Container Grid Stiker A4 Hemat Kertas & Tinta */
        .print-sheet-container {
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        /* Kartu Stiker Minimalis & Hemat (QR Code + Nama + Kode Saja) */
        .sticker-card {
            background-color: #ffffff;
            border: 1.5px solid #000000;
            border-radius: 6px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            page-break-inside: avoid;
            break-inside: avoid;
            gap: 4px;
        }

        .sticker-card-name {
            font-size: 9.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            line-height: 1.2;
            color: #000000;
            max-height: 2.4em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            width: 100%;
        }

        .sticker-card-qr {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            background-color: #ffffff;
            width: 100%;
        }

        .sticker-card-qr img,
        .sticker-card-qr canvas {
            max-width: 115px;
            max-height: 115px;
            width: 115px;
            height: 115px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .sticker-card-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 9.5pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #000000;
            border-top: 1px dashed #94a3b8;
            padding-top: 4px;
            width: 100%;
        }

        /* Aturan Cetak Kertas / Stiker A4 Presisi & Hemat */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .toolbar {
                display: none !important;
            }

            .print-sheet-container {
                max-width: none !important;
                margin: 0 !important;
                display: grid !important;
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 3.5mm !important;
            }

            .sticker-card {
                border: 1px dashed #000000 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            @page {
                size: A4 portrait;
                margin: 6mm;
            }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <div>
            <h1>{{ $title ?? 'Cetak Label QR Barang' }}</h1>
            <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Format hemat: hanya memuat nama aset, QR code, dan kode unik aset.</p>
        </div>
        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                Cetak Sekarang
            </button>
            <a href="/admin/print-asset-qr-codes" class="btn btn-secondary">
                Kembali ke Modul Cetak
            </a>
        </div>
    </div>

    <div class="print-sheet-container" id="printSheet">
        @forelse($assets as $asset)
            <div class="sticker-card" data-code="{{ $asset->getQrScanText() }}">
                {{-- 1. Nama Aset --}}
                <div class="sticker-card-name" title="{{ $asset->name }}">
                    {{ $asset->name }}
                </div>

                {{-- 2. QR Code (Data Payload Teks Lengkap) --}}
                <div class="sticker-card-qr">
                    <div class="print-qr-target" data-code="{{ $asset->getQrScanText() }}" id="qr-item-{{ $asset->id }}"></div>
                </div>

                {{-- 3. Kode Aset --}}
                <div class="sticker-card-code">
                    {{ $asset->asset_code }}
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #64748b;">
                Tidak ada data aset untuk dicetak.
            </div>
        @endforelse
    </div>

    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <script>
        function renderPrintQrs() {
            var targets = document.querySelectorAll('.print-qr-target');
            targets.forEach(function(target) {
                var code = target.getAttribute('data-code');
                if (code && target.innerHTML === '') {
                    new QRCode(target, {
                        text: code,
                        width: 115,
                        height: 115,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }
            });
        }

        window.addEventListener('load', function() {
            renderPrintQrs();
        });
    </script>
</body>
</html>
