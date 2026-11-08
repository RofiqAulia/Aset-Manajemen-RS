<x-filament-panels::page>
    <style>
        /* Scoped Container untuk Modul QR Code */
        .qr-wrapper {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Kotak Header Utama */
        .qr-header-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .qr-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            background-color: #ecfeff;
            color: #0e7490;
            border: 1px solid #cffafe;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            width: fit-content;
        }

        .qr-title-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .qr-title-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .qr-title-icon-box {
            width: 44px;
            height: 44px;
            min-width: 44px;
            max-width: 44px;
            min-height: 44px;
            max-height: 44px;
            border-radius: 12px;
            background-color: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .qr-title-icon-box svg {
            width: 22px !important;
            height: 22px !important;
            max-width: 22px !important;
            max-height: 22px !important;
            display: block;
        }

        .qr-title-text h1 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
        }

        .qr-title-text p {
            font-size: 12.5px;
            color: #64748b;
            margin: 3px 0 0 0;
        }

        .qr-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qr-btn-nav-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s;
        }

        .qr-btn-nav-back:hover {
            background-color: #f8fafc;
        }

        .qr-btn-nav-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            border-radius: 10px;
            background-color: #0284c7;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 4px rgba(2, 132, 199, 0.25);
            transition: all 0.15s;
        }

        .qr-btn-nav-print:hover {
            background-color: #0369a1;
        }

        /* Nav Tabs: Cetak Batch vs Scan QR */
        .qr-nav-tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 4px;
        }

        .qr-tab-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px 8px 0 0;
            border: none;
            background: none;
            cursor: pointer;
            color: #64748b;
            transition: all 0.15s;
        }

        .qr-tab-button.active {
            color: #0284c7;
            border-bottom: 3px solid #0284c7;
            background-color: #f0f9ff;
        }

        /* 4 Kolom Filter Interaktif */
        .qr-filter-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            align-items: end;
        }

        @media (max-width: 1024px) {
            .qr-filter-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 640px) {
            .qr-filter-container {
                grid-template-columns: 1fr;
            }
        }

        .qr-filter-col {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .qr-filter-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .qr-input-field {
            width: 100%;
            padding: 8px 12px;
            font-size: 12.5px;
            font-weight: 500;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background-color: #ffffff;
            color: #0f172a;
            box-sizing: border-box;
            outline: none;
        }

        .qr-input-field:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .qr-select-field {
            width: 100%;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background-color: #ffffff;
            color: #0f172a;
            box-sizing: border-box;
            outline: none;
        }

        .qr-btn-apply-filter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 14px;
            border-radius: 10px;
            background-color: #0284c7;
            color: #ffffff;
            border: none;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s;
        }

        .qr-btn-apply-filter:hover {
            background-color: #0369a1;
        }

        /* Banner Informasi Siap Cetak (Biru Muda) */
        .qr-status-banner {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 12px 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: #0369a1;
            font-size: 13px;
        }

        .qr-status-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qr-status-icon-circle {
            width: 22px;
            height: 22px;
            min-width: 22px;
            border-radius: 9999px;
            background-color: #0284c7;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 12px;
            font-family: serif;
        }

        /* Grid Kartu Stiker Minimalis & Hemat */
        .qr-stickers-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        @media (max-width: 1200px) {
            .qr-stickers-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 800px) {
            .qr-stickers-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .qr-stickers-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Kartu Stiker Hemat: QR Code + Nama + Kode Saja */
        .compact-sticker-card {
            background-color: #ffffff;
            border: 1.5px solid #0f172a;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            page-break-inside: avoid;
            break-inside: avoid;
            gap: 6px;
        }

        .compact-sticker-name {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            max-height: 2.5em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            width: 100%;
        }

        .compact-sticker-qr-holder {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            background-color: #ffffff;
            width: 100%;
        }

        .compact-sticker-qr-holder img,
        .compact-sticker-qr-holder canvas {
            max-width: 110px !important;
            max-height: 110px !important;
            width: 110px !important;
            height: 110px !important;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .compact-sticker-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            font-weight: 800;
            color: #000000;
            letter-spacing: 0.5px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
            width: 100%;
        }

        .btn-single-sticker-print {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 6px 10px;
            margin-top: 6px;
            background-color: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            border-radius: 9999px;
            font-size: 10.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-single-sticker-print:hover {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        /* Area Scanner Kamera Interaktif */
        .scan-module-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .scanner-viewfinder-box {
            position: relative;
            max-width: 480px;
            height: 320px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            background-color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #0284c7;
        }

        .scanner-viewfinder-box video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scanner-laser-line {
            position: absolute;
            left: 20px;
            right: 20px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #38bdf8, #0284c7, #38bdf8, transparent);
            box-shadow: 0 0 8px #38bdf8;
            animation: scanLaser 2s infinite ease-in-out;
        }

        @keyframes scanLaser {
            0% { top: 15%; opacity: 0.3; }
            50% { top: 85%; opacity: 1; }
            100% { top: 15%; opacity: 0.3; }
        }

        /* Aturan Cetak Kertas A4 Presisi (Hemat Ruang & Tinta) */
        @media print {
            body, .fi-body, .fi-main, .fi-page {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .fi-topbar, .fi-sidebar, .fi-header, .no-print-area {
                display: none !important;
            }

            .qr-stickers-grid {
                display: grid !important;
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 4mm !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .compact-sticker-card {
                border: 1px solid #000000 !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .btn-single-sticker-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 6mm;
            }
        }
    </style>

    <div class="qr-wrapper">
        {{-- KOTAK UTAMA: Header, Navigasi Tab, dan Filter --}}
        <div class="no-print-area qr-header-box">
            
            {{-- Bagian Atas: Badge & Judul --}}
            <div class="qr-title-bar">
                <div>
                    {{-- Badge Modul --}}
                    <div class="qr-badge">
                        <span>MANAJEMEN ASET</span>
                        <span style="color: #67e8f9;">•</span>
                        <span>Modul Cetak & Pindai QR Code</span>
                    </div>

                    {{-- Judul & Ikon Terpelihara --}}
                    <div class="qr-title-left" style="margin-top: 8px;">
                        <div class="qr-title-icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-5 0h3v5h-3v-5zm2 5h3v3h-3v-3zm3-3h3v5h-3v-5zm0-2h-3v-2h3v2z"/>
                            </svg>
                        </div>
                        <div class="qr-title-text">
                            <h1>Cetak Batch QR Code Barang</h1>
                            <p>Cetak label stiker hemat (hanya QR code, nama, dan kode) dengan payload teks lengkap saat dipindai HP.</p>
                        </div>
                    </div>
                </div>

                {{-- Tombol Kembali & Cetak A4 --}}
                <div class="qr-header-actions">
                    <a href="/admin/assets" class="qr-btn-nav-back">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Kembali</span>
                    </a>

                    @php
                        $activeAssets = $this->getAssets();
                        $idsParam = $activeAssets->pluck('id')->implode(',');
                        $printA4Url = route('assets.print-qr-bulk', [
                            'ids' => $idsParam,
                            'layout' => $layoutFormat,
                        ]);
                    @endphp

                    <a href="{{ $printA4Url }}" target="_blank" class="qr-btn-nav-print">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Kertas / Stiker A4</span>
                    </a>
                </div>
            </div>

            {{-- Tab Navigasi: Cetak Stiker vs Scan QR --}}
            <div class="qr-nav-tabs">
                <button 
                    type="button" 
                    wire:click="setTab('print')" 
                    class="qr-tab-button {{ $activeTab === 'print' ? 'active' : '' }}"
                >
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Batch Stiker A4 (Hemat)</span>
                </button>

                <button 
                    type="button" 
                    wire:click="setTab('scan')" 
                    class="qr-tab-button {{ $activeTab === 'scan' ? 'active' : '' }}"
                >
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span>Pindai / Scan QR Code Aset</span>
                </button>
            </div>

            @if($activeTab === 'print')
                {{-- 4 Kolom Filter Interaktif --}}
                <div class="qr-filter-container">
                    {{-- 1. Cari Barang / SKU --}}
                    <div class="qr-filter-col">
                        <label class="qr-filter-label">
                            <svg width="13" height="13" fill="none" stroke="#0284c7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>CARI BARANG / SKU</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Ketik kata kunci..." 
                            class="qr-input-field"
                        />
                    </div>

                    {{-- 2. Mode Pilihan Cetak --}}
                    <div class="qr-filter-col">
                        <label class="qr-filter-label">
                            <svg width="13" height="13" fill="none" stroke="#0284c7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <span>MODE PILIHAN CETAK</span>
                        </label>
                        <select wire:model.live="printMode" class="qr-select-field">
                            <option value="all">Semua Item ({{ $this->getBaseQuery()->count() }} Barang)</option>
                            <option value="selected">Hanya Item Terpilih ({{ count($selectedAssetIds) }} Barang)</option>
                        </select>
                    </div>

                    {{-- 3. Pengurutan Data (2 Dropdown) --}}
                    <div class="qr-filter-col">
                        <label class="qr-filter-label">
                            <svg width="13" height="13" fill="none" stroke="#0284c7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            <span>PENGURUTAN DATA</span>
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <select wire:model.live="sortBy" class="qr-select-field">
                                <option value="asset_code">Berdasarkan Kode</option>
                                <option value="name">Berdasarkan Nama</option>
                                <option value="created_at">Berdasarkan Tanggal</option>
                            </select>
                            <select wire:model.live="sortDirection" class="qr-select-field">
                                <option value="asc">A-Z / 1-9</option>
                                <option value="desc">Z-A / 9-1</option>
                            </select>
                        </div>
                    </div>

                    {{-- 4. Layout Kertas / Stiker & Tombol Terapkan --}}
                    <div class="qr-filter-col">
                        <label class="qr-filter-label">
                            <svg width="13" height="13" fill="none" stroke="#0284c7" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span>LAYOUT KERTAS / STIKER</span>
                        </label>
                        <div style="display: flex; gap: 8px;">
                            <select wire:model.live="layoutFormat" class="qr-select-field">
                                <option value="4x6">Stiker 4×6 (24 Stiker / Lembar - Paling Hemat)</option>
                                <option value="3x5">Stiker 3×5 (15 Stiker / Lembar - Standar)</option>
                                <option value="square">Stiker Persegi 1:1 (Roll / Satuan)</option>
                            </select>
                            <button type="button" wire:click="$refresh" class="qr-btn-apply-filter">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Terapkan</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- KONTEN TAB 1: CETAK BATCH STIKER A4 (HEMAT & KOMPAK) --}}
        @if($activeTab === 'print')
            {{-- Info Banner Biru --}}
            <div class="no-print-area qr-status-banner">
                <div class="qr-status-left">
                    <div class="qr-status-icon-circle">i</div>
                    <div>
                        <strong>Siap Dicetak: {{ $this->getTotalAssetsCount() }} Label QR Code (Hanya QR, Nama & Kode)</strong>
                        <span style="color: #7dd3fc; margin: 0 6px;">|</span>
                        <span>Format: {{ strtoupper($layoutFormat) }}</span>
                    </div>
                </div>

                <div style="font-weight: 700;">
                    Estimasi Halaman A4: <span style="font-weight: 900; color: #0c4a6e;">{{ $this->getEstimatedA4Pages() }} Lembar</span>
                </div>
            </div>

            {{-- Pesan Panduan Pemindaian HP --}}
            <div class="no-print-area" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 16px; font-size: 12px; color: #475569; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 16px;">💡</span>
                <span><strong>Desain Hemat:</strong> Dicetak ringkas hanya menampilkan QR code, nama aset, dan kode aset. Saat dipindai dengan <strong>kamera HP apa saja</strong>, teks yang muncul otomatis memuat rincian lengkap (Kode, Nama, Kategori, Lokasi, Unit, Kondisi, dan Status).</span>
            </div>

            {{-- Kisi Kartu Stiker Hemat (QR Code + Nama + Kode) --}}
            <div class="qr-stickers-grid" id="stickersGrid">
                @forelse($this->getAssets() as $asset)
                    <div style="display: flex; flex-direction: column;">
                        {{-- Kartu Stiker Kompak --}}
                        <div class="compact-sticker-card">
                            {{-- 1. Nama Aset --}}
                            <div class="compact-sticker-name" title="{{ $asset->name }}">
                                {{ $asset->name }}
                            </div>

                            {{-- 2. QR Code (Payload Memuat Teks Lengkap) --}}
                            <div class="compact-sticker-qr-holder">
                                <div class="qr-render-item" data-code="{{ $asset->getQrScanText() }}" id="qr-dom-{{ $asset->id }}"></div>
                            </div>

                            {{-- 3. Kode Aset --}}
                            <div class="compact-sticker-code">
                                {{ $asset->asset_code }}
                            </div>
                        </div>

                        {{-- Tombol Cetak Stiker Single --}}
                        <div class="no-print-area">
                            <a 
                                href="{{ route('assets.print-qr', ['asset' => $asset->id, 'size' => 'single']) }}" 
                                target="_blank" 
                                class="btn-single-sticker-print"
                            >
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                <span>Cetak Stiker Single</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 48px; text-align: center; color: #64748b; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1;">
                        Tidak ada data barang / aset yang sesuai dengan kriteria filter.
                    </div>
                @endforelse
            </div>
        @endif

        {{-- KONTEN TAB 2: PINDAI / SCAN QR CODE ASET --}}
        @if($activeTab === 'scan')
            <div class="no-print-area scan-module-card">
                <div style="text-align: center; max-width: 600px; margin: 0 auto; display: flex; flex-direction: column; gap: 8px;">
                    <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0;">Pindai QR Code Menggunakan Kamera atau Alat Scanner</h2>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">Arahkan kamera ke stiker QR aset RS UMMI, atau ketik / gunakan scanner barcode USB untuk melihat detail aset langsung.</p>
                </div>

                {{-- Kotak Viewfinder Kamera --}}
                <div class="scanner-viewfinder-box" id="scannerViewport">
                    <video id="scannerVideo" playsinline></video>
                    <div class="scanner-laser-line"></div>
                    <div id="cameraStatusMsg" style="position: absolute; bottom: 12px; left: 16px; right: 16px; font-size: 11px; color: #bae6fd; text-align: center; background: rgba(15, 23, 42, 0.7); padding: 4px 8px; border-radius: 6px;">
                        Kamera aktif. Posisikan QR Code di dalam kotak.
                    </div>
                </div>

                {{-- Input Barcode Manual / Scanner USB --}}
                <div style="max-width: 480px; margin: 0 auto; width: 100%; display: flex; gap: 8px;">
                    <input 
                        type="text" 
                        wire:model="scanInput" 
                        wire:keydown.enter="processScan" 
                        placeholder="Contoh: AST-2026-000001 lalu tekan Enter..." 
                        class="qr-input-field" 
                        style="padding: 10px 14px; font-size: 13px; font-weight: 700;"
                    />
                    <button type="button" wire:click="processScan" class="qr-btn-apply-filter" style="padding: 10px 18px;">
                        Cari Aset
                    </button>
                    @if($scannedAsset)
                        <button type="button" wire:click="clearScan" class="qr-btn-nav-back" style="padding: 10px 14px;">
                            Reset
                        </button>
                    @endif
                </div>

                {{-- Hasil Pemindaian Aset --}}
                @if($scannedAsset)
                    <div style="max-width: 600px; margin: 10px auto 0 auto; width: 100%; background: #ffffff; border: 2px solid #0284c7; border-radius: 14px; padding: 20px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.1);">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <span style="font-size: 10px; font-weight: 800; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                                    {{ $scannedAsset->category?->name ?? 'Aset RS UMMI' }}
                                </span>
                                <h3 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 4px 0 0 0;">
                                    {{ $scannedAsset->name }}
                                </h3>
                                <p style="font-family: monospace; font-size: 13px; font-weight: 800; color: #0284c7; margin: 2px 0 0 0;">
                                    {{ $scannedAsset->asset_code }}
                                </p>
                            </div>

                            <a href="/admin/assets/{{ $scannedAsset->id }}" target="_blank" class="qr-btn-apply-filter" style="padding: 6px 12px; font-size: 11px;">
                                Buka Detail Aset ↗
                            </a>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; background: #f8fafc; padding: 12px; border-radius: 8px;">
                            <div><strong>Lokasi:</strong> {{ $scannedAsset->location?->name ?? '-' }}</div>
                            <div><strong>Unit:</strong> {{ $scannedAsset->department?->name ?? '-' }}</div>
                            <div><strong>Kondisi:</strong> <span style="text-transform: capitalize; font-weight: 700;">{{ $scannedAsset->condition->value ?? $scannedAsset->condition }}</span></div>
                            <div><strong>Status:</strong> <span style="text-transform: capitalize; font-weight: 700;">{{ $scannedAsset->status->value ?? $scannedAsset->status }}</span></div>
                            <div><strong>Merek/Model:</strong> {{ $scannedAsset->brand ?? '-' }} {{ $scannedAsset->model ? '('.$scannedAsset->model.')' : '' }}</div>
                            <div><strong>Nomor Seri:</strong> {{ $scannedAsset->serial_number ?? '-' }}</div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="{{ asset('js/qrcode.min.js') }}"></script>
        <script>
            function drawAllQrImages() {
                var elements = document.querySelectorAll('.qr-render-item');
                elements.forEach(function(el) {
                    var code = el.getAttribute('data-code');
                    if (code && el.innerHTML === '') {
                        new QRCode(el, {
                            text: code,
                            width: 110,
                            height: 110,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', drawAllQrImages);
            document.addEventListener('livewire:navigated', drawAllQrImages);
            if (window.Livewire) {
                Livewire.hook('morph.updated', function() {
                    setTimeout(drawAllQrImages, 60);
                });
            }

            // Inisialisasi Kamera Pemindai (BarcodeDetector / MediaDevices)
            var videoElement = document.getElementById('scannerVideo');
            var scannerStream = null;

            function startCameraScanner() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    var msg = document.getElementById('cameraStatusMsg');
                    if (msg) msg.innerText = 'Kamera tidak didukung pada browser ini. Gunakan input kode di bawah.';
                    return;
                }

                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                    .then(function(stream) {
                        scannerStream = stream;
                        var vid = document.getElementById('scannerVideo');
                        if (vid) {
                            vid.srcObject = stream;
                            vid.play();
                            initBarcodeDetector(vid);
                        }
                    })
                    .catch(function(err) {
                        var msg = document.getElementById('cameraStatusMsg');
                        if (msg) msg.innerText = 'Izin kamera belum aktif. Gunakan input manual atau aktifkan izin kamera.';
                    });
            }

            function initBarcodeDetector(video) {
                if ('BarcodeDetector' in window) {
                    var detector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'ean_13'] });
                    var detectInterval = setInterval(function() {
                        if (video.readyState >= 2) {
                            detector.detect(video).then(function(barcodes) {
                                if (barcodes.length > 0) {
                                    var scannedText = barcodes[0].rawValue;
                                    clearInterval(detectInterval);
                                    if (window.Livewire) {
                                        @this.set('scanInput', scannedText);
                                        @this.processScan();
                                    }
                                }
                            }).catch(function() {});
                        }
                    }, 500);
                }
            }

            // Jalankan scanner jika tab scan aktif
            window.addEventListener('load', function() {
                if (document.getElementById('scannerVideo')) {
                    startCameraScanner();
                }
            });
        </script>
    @endpush
</x-filament-panels::page>
