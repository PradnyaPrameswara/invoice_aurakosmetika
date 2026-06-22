<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceSalesExporter
{
    /**
     * @param Collection<int, \App\Models\Invoice> $invoices
     */
    public function download(Collection $invoices, string $filename): BinaryFileResponse
    {
        $spreadsheet = $this->buildSpreadsheet($invoices);

        $tmpBase = tempnam(sys_get_temp_dir(), 'invoice_export_');
        if ($tmpBase === false) {
            abort(500, 'Gagal membuat file sementara untuk export.');
        }
        $tmpPath = $tmpBase . '.xlsx';
        @rename($tmpBase, $tmpPath);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($tmpPath);

        return response()->download(
            $tmpPath,
            $filename,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        )->deleteFileAfterSend(true);
    }

    /**
     * @param Collection<int, \App\Models\Invoice> $invoices
     */
    private function buildSpreadsheet(Collection $invoices): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Penjualan');

        $totalPendapatanKeseluruhan = (float) $invoices
            ->filter(fn ($invoice) => $invoice->status === 'lunas')
            ->sum(fn ($invoice) => (float) $invoice->total_tagihan);

        $headings = [
            'No Invoice',
            'Tanggal Terbit',
            'Nama Pelanggan',
            'Status',
            'Sub Total',
            'Total Pendapatan',
        ];

        $sheet->fromArray($headings, null, 'A1');

        $row = 2;
        foreach ($invoices as $invoice) {
            $tanggalTerbit = $invoice->tanggal_terbit ? $invoice->tanggal_terbit->format('Y-m-d') : '';
            $namaPelanggan = $invoice->pelanggan?->nama_pelanggan ?? '';

            $this->writeRow($sheet, $row++, [
                $invoice->no_invoice,
                $tanggalTerbit,
                $namaPelanggan,
                $invoice->status_label,
                (float) $invoice->total_tagihan,
                '',
            ]);
        }

        // Single total row (shown once)
        $this->writeRow($sheet, $row, [
            'TOTAL PENDAPATAN (LUNAS)',
            '',
            '',
            '',
            '',
            $totalPendapatanKeseluruhan,
        ]);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);

        // Rupiah formatting for money columns (Sub Total + Total Pendapatan)
        $sheet->getStyle('E2:F' . $row)
            ->getNumberFormat()
            ->setFormatCode('"Rp" #,##0.00');

        $sheet->freezePane('A2');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 2: Pendapatan Bulanan + Chart
        $monthlySheet = $spreadsheet->createSheet();
        $monthlySheet->setTitle('Pendapatan Bulanan');

        $monthlySheet->fromArray(['Bulan', 'Pendapatan (Lunas)'], null, 'A1');
        $monthlySheet->getStyle('A1:B1')->getFont()->setBold(true);
        $monthlySheet->freezePane('A2');

        /**
         * @var array<string, float> $monthlyTotals
         */
        $monthlyTotals = [];
        foreach ($invoices as $invoice) {
            if ($invoice->status !== 'lunas') {
                continue;
            }

            if (!$invoice->tanggal_terbit) {
                continue;
            }

            $key = $invoice->tanggal_terbit->format('Y-m');
            $monthlyTotals[$key] = ($monthlyTotals[$key] ?? 0.0) + (float) $invoice->total_tagihan;
        }

        ksort($monthlyTotals);

        $years = array_values(array_unique(array_map(
            static fn (string $ym): string => substr($ym, 0, 4),
            array_keys($monthlyTotals)
        )));
        $includeYearInLabel = count($years) > 1;

        $monthNamesId = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $row = 2;
        foreach ($monthlyTotals as $ym => $total) {
            [$year, $month] = explode('-', $ym);
            $monthInt = (int) $month;
            $label = $monthNamesId[$monthInt] ?? $ym;
            if ($includeYearInLabel) {
                $label .= ' ' . $year;
            }

            $this->writeRow($monthlySheet, $row++, [$label, $total]);
        }

        $lastDataRow = $row - 1;
        if ($lastDataRow >= 2) {
            $monthlySheet->getStyle('B2:B' . $lastDataRow)
                ->getNumberFormat()
                ->setFormatCode('"Rp" #,##0.00');

            $monthCount = $lastDataRow - 1;

            $labels = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Pendapatan Bulanan'!\$B\$1", null, 1),
            ];
            $categories = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Pendapatan Bulanan'!\$A\$2:\$A\$$lastDataRow", null, $monthCount),
            ];
            $values = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Pendapatan Bulanan'!\$B\$2:\$B\$$lastDataRow", null, $monthCount),
            ];

            $series = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($values) - 1),
                $labels,
                $categories,
                $values
            );
            $series->setPlotDirection(DataSeries::DIRECTION_COL);

            $plotArea = new PlotArea(null, [$series]);
            $legend = new Legend(Legend::POSITION_RIGHT, null, false);
            $title = new Title('Pendapatan Bulanan (Lunas)');
            $yAxisLabel = new Title('Rupiah');

            $chart = new Chart(
                'pendapatan_bulanan_chart',
                $title,
                $legend,
                $plotArea,
                true,
                0,
                null,
                $yAxisLabel
            );

            $chart->setTopLeftPosition('D2');
            $chart->setBottomRightPosition('L20');

            $monthlySheet->addChart($chart);
        } else {
            $this->writeRow($monthlySheet, 2, ['-', 0]);
            $monthlySheet->getStyle('B2')->getNumberFormat()->setFormatCode('"Rp" #,##0.00');
        }

        foreach (range('A', 'B') as $col) {
            $monthlySheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Write a row starting at column A.
     *
     * @param array<int, mixed> $values
     */
    private function writeRow($sheet, int $row, array $values): void
    {
        $col = 'A';
        foreach ($values as $value) {
            if (is_string($value)) {
                $sheet->setCellValueExplicit($col . $row, $value, DataType::TYPE_STRING);
            } else {
                $sheet->setCellValue($col . $row, $value);
            }
            $col++;
        }
    }
}
