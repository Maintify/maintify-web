<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * Generate and stream a CSV report as a downloadable response.
     *
     * @param  array  $reportData  Aggregated report data from the ReportController.
     * @return StreamedResponse
     */
    public function downloadCsv(array $reportData, string $filename = 'laporan-operasional.csv')
    {
        return response()->streamDownload(function () use ($reportData) {
            $out = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fwrite($out, "\xEF\xBB\xBF");

            // ─── Section 1: Summary ───────────────────────────────────────────
            fputcsv($out, ['=== RINGKASAN LAPORAN ===']);
            fputcsv($out, ['Periode', $reportData['period_label']]);
            fputcsv($out, ['Total Servis', $reportData['total_services']]);
            fputcsv($out, ['Total Pendapatan', $reportData['total_revenue_formatted']]);
            fputcsv($out, ['Rata-rata Pendapatan / Servis', $reportData['avg_revenue_formatted']]);
            fputcsv($out, []);

            // ─── Section 2: Breakdown per Jenis Servis ────────────────────────
            fputcsv($out, ['=== BREAKDOWN PER JENIS SERVIS ===']);
            fputcsv($out, ['Jenis Servis', 'Jumlah', 'Pendapatan']);
            foreach ($reportData['by_type'] as $row) {
                fputcsv($out, [
                    $row['label'],
                    $row['count'],
                    'Rp '.number_format($row['revenue'], 0, ',', '.'),
                ]);
            }
            fputcsv($out, []);

            // ─── Section 3: Daily Timeline ────────────────────────────────────
            fputcsv($out, ['=== SERVIS PER HARI ===']);
            fputcsv($out, ['Tanggal', 'Jumlah Servis', 'Pendapatan']);
            foreach ($reportData['daily'] as $day) {
                fputcsv($out, [
                    $day['date'],
                    $day['count'],
                    'Rp '.number_format($day['revenue'], 0, ',', '.'),
                ]);
            }
            fputcsv($out, []);

            // ─── Section 4: Top Spareparts ────────────────────────────────────
            fputcsv($out, ['=== TOP SPAREPART / SUKU CADANG ===']);
            fputcsv($out, ['Nama Sparepart', 'Total Qty Digunakan', 'Total Nilai']);
            foreach ($reportData['top_parts'] as $part) {
                fputcsv($out, [
                    $part->part_name,
                    $part->total_qty,
                    'Rp '.number_format($part->total_value, 0, ',', '.'),
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
