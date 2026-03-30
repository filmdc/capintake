<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Exports\NpiReportCsvExport;
use App\Services\NpiReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NpiReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'NPI Report';

    protected static ?string $title = 'NPI Performance Report';

    protected string $view = 'filament.pages.npi-report';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public ?string $preset = 'fiscal_year';

    public ?Collection $reportData = null;

    public ?int $grandTotal = null;

    public function mount(): void
    {
        $this->applyPreset('fiscal_year');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath(null)
            ->components([
                Section::make('Report Parameters')
                    ->schema([
                        Select::make('preset')
                            ->label('Quick Select')
                            ->options([
                                'fiscal_year' => 'Current Fiscal Year (Oct–Sep)',
                                'calendar_year' => 'Current Calendar Year',
                                'last_quarter' => 'Last Quarter',
                                'this_quarter' => 'This Quarter',
                                'last_month' => 'Last Month',
                                'this_month' => 'This Month',
                                'custom' => 'Custom Date Range',
                            ])
                            ->default('fiscal_year')
                            ->live()
                            ->afterStateUpdated(fn (?string $state) => $this->applyPreset($state)),

                        DatePicker::make('startDate')
                            ->label('Start Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('m/d/Y'),

                        DatePicker::make('endDate')
                            ->label('End Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('m/d/Y'),
                    ])
                    ->columns(3),
            ]);
    }

    public function applyPreset(?string $preset): void
    {
        $now = now();

        match ($preset) {
            'fiscal_year' => (function () use ($now): void {
                $this->startDate = ($now->month >= 10 ? $now->copy()->startOfYear()->addMonths(9) : $now->copy()->subYear()->startOfYear()->addMonths(9))->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
            })(),
            'calendar_year' => (function () use ($now): void {
                $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
            })(),
            'last_quarter' => (function () use ($now): void {
                $this->startDate = $now->copy()->subQuarter()->startOfQuarter()->format('Y-m-d');
                $this->endDate = $now->copy()->subQuarter()->endOfQuarter()->format('Y-m-d');
            })(),
            'this_quarter' => (function () use ($now): void {
                $this->startDate = $now->copy()->startOfQuarter()->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
            })(),
            'last_month' => (function () use ($now): void {
                $this->startDate = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
            })(),
            'this_month' => (function () use ($now): void {
                $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
            })(),
            default => null,
        };

        $this->reportData = null;
        $this->grandTotal = null;
    }

    public function generateReport(): void
    {
        if (! $this->startDate || ! $this->endDate) {
            Notification::make()->danger()->title('Please select a date range.')->send();

            return;
        }

        $service = new NpiReportService();
        $this->reportData = $service->generate($this->startDate, $this->endDate);
        $this->grandTotal = $service->grandTotalUnduplicatedClients($this->startDate, $this->endDate);

        Notification::make()
            ->success()
            ->title('Report generated')
            ->body('Showing data for ' . $this->startDate . ' to ' . $this->endDate)
            ->send();
    }

    public function exportPdf(): StreamedResponse
    {
        $service = new NpiReportService();
        $report = $service->generate($this->startDate, $this->endDate);
        $grandTotal = $service->grandTotalUnduplicatedClients($this->startDate, $this->endDate);

        $pdf = Pdf::loadView('reports.npi-report-pdf', [
            'report' => $report,
            'grandTotal' => $grandTotal,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ])->setPaper('letter', 'landscape');

        $filename = 'npi-report-' . $this->startDate . '-to-' . $this->endDate . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function exportCsv(): BinaryFileResponse
    {
        $filename = 'npi-report-' . $this->startDate . '-to-' . $this->endDate . '.xlsx';

        return Excel::download(
            new NpiReportCsvExport($this->startDate, $this->endDate),
            $filename,
        );
    }
}
