<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\MaintenanceLogStatus;
use App\Models\MaintenanceLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DailyActivityLogWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MaintenanceLog::query()
                    ->with(['asset.location', 'technician'])
                    ->latest('performed_at')
            )
            ->heading('Log Aktivitas Harian Pemeliharaan Aset')
            ->description('Pencatatan riwayat pemeliharaan, inspeksi teknis, dan perbaikan harian aset rumah sakit.')
            ->paginationMode(PaginationMode::Default)
            ->paginationPageOptions([5, 10, 25, 50])
            ->defaultPaginationPageOption(5)
            ->extremePaginationLinks()
            ->columns([
                TextColumn::make('performed_at')
                    ->label('Tanggal Pelaksanaan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('asset.asset_code')
                    ->label('Kode Aset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.name')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('asset.location.name')
                    ->label('Ruangan / Lokasi')
                    ->sortable(),
                TextColumn::make('technician.name')
                    ->label('Teknisi Pelaksana')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('findings')
                    ->label('Temuan & Kondisi')
                    ->limit(40)
                    ->tooltip(fn ($record): ?string => $record->findings),
                TextColumn::make('action_taken')
                    ->label('Tindakan Diambil')
                    ->limit(40)
                    ->tooltip(fn ($record): ?string => $record->action_taken),
                TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('performed_at_range')
                    ->label('Filter Rentang Tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('performed_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('performed_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(MaintenanceLogStatus::class),
            ])
            ->headerActions([
                Action::make('export_pdf')
                    ->label('Cetak Laporan PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function ($livewire) {
                        $logs = $livewire->getFilteredTableQuery()
                            ->with(['asset.location', 'technician'])
                            ->get();

                        $filters = $livewire->getTableFilterState('performed_at_range');
                        $startDate = $filters['dari_tanggal'] ?? null;
                        $endDate = $filters['sampai_tanggal'] ?? null;

                        return response()->streamDownload(function () use ($logs, $startDate, $endDate) {
                            $logoPath = public_path('assets/img/logo-rs-ummi-png.png');
                            $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

                            $pdf = Pdf::loadView('reports.daily-activity-pdf', [
                                'logs' => $logs,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                                'logoBase64' => $logoBase64,
                                'printedAt' => now()->translatedFormat('d F Y H:i:s'),
                            ])->setPaper('a4', 'landscape');

                            echo $pdf->output();
                        }, 'Laporan_Log_Aktivitas_Harian_SIMAS_RS_'.now()->format('Ymd_His').'.pdf');
                    }),
            ]);
    }
}
