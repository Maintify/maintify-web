<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * Generate and stream a professional binary Excel (.xlsx) report using PhpSpreadsheet.
     *
     * @param  array  $reportData  Aggregated report data from the ReportController.
     * @param  string  $filename  The filename for download.
     * @return StreamedResponse
     */
    public function downloadExcel(array $reportData, string $filename = 'laporan-operasional.xlsx')
    {
        if (! str_ends_with($filename, '.xlsx')) {
            $filename = str_replace('.csv', '.xlsx', $filename);
            if (! str_ends_with($filename, '.xlsx')) {
                $filename .= '.xlsx';
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Operasional');
        $sheet->setShowGridLines(true);

        // ── Styles Definition ──────────────────────────────────────────────
        // Header Tabel: Dark Navy (#1F4E78), Teks Putih Bold
        $headerStyleLeft = [
            'font' => [
                'name' => 'Calibri',
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF94A3B8'],
                ],
            ],
        ];

        $headerStyleCenter = array_merge($headerStyleLeft, [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $headerStyleRight = array_merge($headerStyleLeft, [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Header Seksi (Dark Slate)
        $sectionHeaderStyle = [
            'font' => [
                'name' => 'Calibri',
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF334155'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        // Thin Border Sel Data
        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ];

        // Total Row (Bold, Light Grey Fill, Top Thin Border, Bottom Double Border)
        $totalRowStyle = [
            'font' => [
                'name' => 'Calibri',
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF3F4F6'],
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF9CA3AF'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['argb' => 'FF111827'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD1D5DB'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ];

        $currencyFormat = '"Rp "#,##0';
        $integerFormat = '#,##0';

        $row = 1;

        // ── A. Judul & Periode Laporan (Baris 1-2) ─────────────────────────
        $sheet->setCellValue("A{$row}", 'REKAPITULASI LAPORAN OPERASIONAL BENGKEL');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF1F4E78'));
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $row++;

        $sheet->setCellValue("A{$row}", 'Periode Laporan: '.$reportData['period_label']);
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setColor(new Color('FF4B5563'));
        $row += 3; // 2 blank rows spacing

        // ── B. Seksi 1: RINGKASAN LAPORAN ──────────────────────────────────
        $sheet->setCellValue("A{$row}", '1. RINGKASAN LAPORAN');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($sectionHeaderStyle);
        $row++;

        $sheet->setCellValue("A{$row}", 'Metrik Laporan');
        $sheet->setCellValue("B{$row}", 'Jumlah Servis');
        $sheet->setCellValue("C{$row}", 'Total Nilai');
        $sheet->getStyle("A{$row}")->applyFromArray($headerStyleLeft);
        $sheet->getStyle("B{$row}")->applyFromArray($headerStyleCenter);
        $sheet->getStyle("C{$row}")->applyFromArray($headerStyleRight);
        $row++;

        // Total Servis
        $sheet->setCellValue("A{$row}", 'Total Servis Ditangani');
        $sheet->setCellValue("B{$row}", (int) $reportData['total_services']);
        $sheet->setCellValue("C{$row}", '-');
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($thinBorder);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($integerFormat);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        // Total Pendapatan
        $sheet->setCellValue("A{$row}", 'Total Pendapatan Bengkel');
        $sheet->setCellValue("B{$row}", '-');
        $sheet->setCellValue("C{$row}", (float) $reportData['total_revenue']);
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($thinBorder);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        // Rata-rata Pendapatan
        $sheet->setCellValue("A{$row}", 'Rata-rata Pendapatan / Servis');
        $sheet->setCellValue("B{$row}", '-');
        $sheet->setCellValue("C{$row}", (float) $reportData['avg_revenue']);
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($thinBorder);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row += 3; // 2 blank rows spacing

        // ── C. Seksi 2: BREAKDOWN PER JENIS SERVIS ─────────────────────────
        $sheet->setCellValue("A{$row}", '2. BREAKDOWN PER JENIS SERVIS');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($sectionHeaderStyle);
        $row++;

        $sheet->setCellValue("A{$row}", 'Jenis Servis');
        $sheet->setCellValue("B{$row}", 'Jumlah Servis');
        $sheet->setCellValue("C{$row}", 'Total Pendapatan');
        $sheet->getStyle("A{$row}")->applyFromArray($headerStyleLeft);
        $sheet->getStyle("B{$row}")->applyFromArray($headerStyleCenter);
        $sheet->getStyle("C{$row}")->applyFromArray($headerStyleRight);
        $row++;

        $typeStartRow = $row;
        foreach ($reportData['by_type'] as $typeRow) {
            $sheet->setCellValue("A{$row}", $typeRow['label']);
            $sheet->setCellValue("B{$row}", (int) $typeRow['count']);
            $sheet->setCellValue("C{$row}", (float) $typeRow['revenue']);

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($thinBorder);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($integerFormat);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }

        // Total Row Section 2
        $typeEndRow = $row - 1;
        $sheet->setCellValue("A{$row}", 'TOTAL');
        if ($typeEndRow >= $typeStartRow) {
            $sheet->setCellValue("B{$row}", "=SUM(B{$typeStartRow}:B{$typeEndRow})");
            $sheet->setCellValue("C{$row}", "=SUM(C{$typeStartRow}:C{$typeEndRow})");
        } else {
            $sheet->setCellValue("B{$row}", 0);
            $sheet->setCellValue("C{$row}", 0);
        }
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($totalRowStyle);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($integerFormat);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row += 3; // 2 blank rows spacing

        // ── D. Seksi 3: SERVIS PER HARI ────────────────────────────────────
        $sheet->setCellValue("A{$row}", '3. SERVIS PER HARI');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($sectionHeaderStyle);
        $row++;

        $sheet->setCellValue("A{$row}", 'Tanggal Servis');
        $sheet->setCellValue("B{$row}", 'Jumlah Servis');
        $sheet->setCellValue("C{$row}", 'Total Pendapatan');
        $sheet->getStyle("A{$row}")->applyFromArray($headerStyleCenter);
        $sheet->getStyle("B{$row}")->applyFromArray($headerStyleCenter);
        $sheet->getStyle("C{$row}")->applyFromArray($headerStyleRight);
        $row++;

        $dailyStartRow = $row;
        foreach ($reportData['daily'] as $day) {
            $sheet->setCellValue("A{$row}", $day['date']);
            $sheet->setCellValue("B{$row}", (int) $day['count']);
            $sheet->setCellValue("C{$row}", (float) $day['revenue']);

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($thinBorder);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($integerFormat);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }

        // Total Row Section 3
        $dailyEndRow = $row - 1;
        $sheet->setCellValue("A{$row}", 'TOTAL');
        if ($dailyEndRow >= $dailyStartRow) {
            $sheet->setCellValue("B{$row}", "=SUM(B{$dailyStartRow}:B{$dailyEndRow})");
            $sheet->setCellValue("C{$row}", "=SUM(C{$dailyStartRow}:C{$dailyEndRow})");
        } else {
            $sheet->setCellValue("B{$row}", 0);
            $sheet->setCellValue("C{$row}", 0);
        }
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($totalRowStyle);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($integerFormat);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row += 3; // 2 blank rows spacing

        // ── E. Seksi 4: TOP SPAREPART / SUKU CADANG ────────────────────────
        $parts = $reportData['all_parts'] ?? $reportData['top_parts'];
        $sheet->setCellValue("A{$row}", '4. TOP SPAREPART / SUKU CADANG');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($sectionHeaderStyle);
        $row++;

        $sheet->setCellValue("A{$row}", 'Nama Sparepart');
        $sheet->setCellValue("B{$row}", 'Total Qty Digunakan');
        $sheet->setCellValue("C{$row}", 'Total Nilai');
        $sheet->getStyle("A{$row}")->applyFromArray($headerStyleLeft);
        $sheet->getStyle("B{$row}")->applyFromArray($headerStyleCenter);
        $sheet->getStyle("C{$row}")->applyFromArray($headerStyleRight);
        $row++;

        $partStartRow = $row;
        if (count($parts) === 0) {
            $sheet->setCellValue("A{$row}", 'Tidak ada data sparepart');
            $sheet->setCellValue("B{$row}", 0);
            $sheet->setCellValue("C{$row}", 0);
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($thinBorder);
            $row++;
        } else {
            foreach ($parts as $idx => $part) {
                $sheet->setCellValue("A{$row}", $part->part_name);
                $sheet->setCellValue("B{$row}", (int) $part->total_qty);
                $sheet->setCellValue("C{$row}", (float) $part->total_value);

                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($thinBorder);
                $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($integerFormat);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Zebra striping (#F2F4F7) for odd rows
                if ($idx % 2 === 1) {
                    $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F4F7');
                }
                $row++;
            }
        }

        // Total Row Section 4
        $partEndRow = $row - 1;
        $sheet->setCellValue("A{$row}", 'TOTAL');
        if ($partEndRow >= $partStartRow) {
            $sheet->setCellValue("B{$row}", "=SUM(B{$partStartRow}:B{$partEndRow})");
            $sheet->setCellValue("C{$row}", "=SUM(C{$partStartRow}:C{$partEndRow})");
        } else {
            $sheet->setCellValue("B{$row}", 0);
            $sheet->setCellValue("C{$row}", 0);
        }
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($totalRowStyle);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($integerFormat);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto-fit Column Widths
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate temporary file for reliable download across all web servers (PHP CLI dev server, Apache, Nginx, LiteSpeed)
        $tempPath = tempnam(sys_get_temp_dir(), 'maintify_report_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, must-revalidate',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Backward compatibility method alias.
     */
    public function downloadCsv(array $reportData, string $filename = 'laporan-operasional.xlsx')
    {
        return $this->downloadExcel($reportData, $filename);
    }
}
