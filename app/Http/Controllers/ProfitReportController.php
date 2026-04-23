<?php

namespace App\Http\Controllers;

use App\Models\DetailSale;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfitReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = $this->buildReport($request);

        return view('admin.reports.profit', $report);
    }

    public function exportExcel(Request $request): Response
    {
        $report = $this->buildReport($request);
        $filename = 'laporan-profit-' . now()->format('Ymd-His') . '.xls';
        $content = view('admin.reports.profit_excel', $report)->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $report = $this->buildReport($request);
        $filename = 'laporan-profit-' . now()->format('Ymd-His') . '.pdf';
        $pdf = $this->buildSimplePdf($report);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function buildReport(Request $request): array
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sort = $request->input('sort', 'highest');
        $direction = $sort === 'lowest' ? 'asc' : 'desc';

        $baseQuery = DetailSale::query()
            ->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id')
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('sales.created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('sales.created_at', '<=', $dateTo);
            });

        $summary = (clone $baseQuery)
            ->selectRaw('COALESCE(SUM(detail_sales.sub_total), 0) as total_omzet')
            ->selectRaw('COALESCE(SUM(detail_sales.buy_price * detail_sales.quantity), 0) as total_modal')
            ->first();

        $totalOmzet = (float) ($summary->total_omzet ?? 0);
        $totalModal = (float) ($summary->total_modal ?? 0);
        $grossProfit = $totalOmzet - $totalModal;
        $margin = $totalOmzet > 0 ? ($grossProfit / $totalOmzet) * 100 : 0;

        $products = (clone $baseQuery)
            ->select(
                'detail_sales.product_id',
                'detail_sales.product_name',
                DB::raw('SUM(detail_sales.quantity) as qty_sold'),
                DB::raw('SUM(detail_sales.sub_total) as omzet'),
                DB::raw('SUM(detail_sales.buy_price * detail_sales.quantity) as modal'),
                DB::raw('SUM(detail_sales.product_profit) as laba'),
                DB::raw('CASE WHEN SUM(detail_sales.sub_total) > 0 THEN (SUM(detail_sales.product_profit) / SUM(detail_sales.sub_total)) * 100 ELSE 0 END as margin_percent')
            )
            ->groupBy('detail_sales.product_id', 'detail_sales.product_name')
            ->orderBy('margin_percent', $direction)
            ->orderBy('laba', 'desc')
            ->get();

        return [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sort' => $sort,
            'periodLabel' => $this->buildPeriodLabel($dateFrom, $dateTo),
            'summary' => [
                'total_omzet' => $totalOmzet,
                'total_modal' => $totalModal,
                'gross_profit' => $grossProfit,
                'margin' => $margin,
            ],
            'products' => $products,
        ];
    }

    protected function buildPeriodLabel(?string $dateFrom, ?string $dateTo): string
    {
        if ($dateFrom && $dateTo) {
            return date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo));
        }

        if ($dateFrom) {
            return 'Mulai ' . date('d/m/Y', strtotime($dateFrom));
        }

        if ($dateTo) {
            return 'Sampai ' . date('d/m/Y', strtotime($dateTo));
        }

        return 'Semua periode';
    }

    protected function buildSimplePdf(array $report): string
    {
        $pageWidth = 842;
        $pageHeight = 595;
        $marginLeft = 32;
        $marginTop = 32;
        $lineHeight = 14;
        $rowsPerPage = 26;

        $headerLines = [
            'LAPORAN PROFIT',
            'Periode: ' . $report['periodLabel'],
            sprintf(
                'Omzet: %s   Modal: %s   Laba Kotor: %s   Margin: %s%%',
                $this->formatRupiah($report['summary']['total_omzet']),
                $this->formatRupiah($report['summary']['total_modal']),
                $this->formatRupiah($report['summary']['gross_profit']),
                number_format((float) $report['summary']['margin'], 2, ',', '.')
            ),
            '',
            str_pad('No', 4)
                . str_pad('Produk', 28)
                . str_pad('Qty', 8, ' ', STR_PAD_LEFT)
                . str_pad('Omzet', 18, ' ', STR_PAD_LEFT)
                . str_pad('Modal', 18, ' ', STR_PAD_LEFT)
                . str_pad('Laba', 18, ' ', STR_PAD_LEFT)
                . str_pad('Margin', 10, ' ', STR_PAD_LEFT),
            str_repeat('-', 104),
        ];

        $bodyLines = [];

        foreach ($report['products'] as $index => $product) {
            $bodyLines[] =
                str_pad((string) ($index + 1), 4)
                . str_pad($this->truncateText($product->product_name, 27), 28)
                . str_pad(number_format((float) $product->qty_sold, 0, ',', '.'), 8, ' ', STR_PAD_LEFT)
                . str_pad($this->formatRupiah($product->omzet), 18, ' ', STR_PAD_LEFT)
                . str_pad($this->formatRupiah($product->modal), 18, ' ', STR_PAD_LEFT)
                . str_pad($this->formatRupiah($product->laba), 18, ' ', STR_PAD_LEFT)
                . str_pad(number_format((float) $product->margin_percent, 2, ',', '.') . '%', 10, ' ', STR_PAD_LEFT);
        }

        if (empty($bodyLines)) {
            $bodyLines[] = 'Belum ada data penjualan pada periode ini.';
        }

        $chunks = array_chunk($bodyLines, $rowsPerPage);
        $pages = [];

        foreach ($chunks as $pageIndex => $chunk) {
            $lines = $headerLines;

            if ($pageIndex > 0) {
                $lines[0] .= ' (lanjutan)';
            }

            $lines = array_merge($lines, $chunk);
            $pages[] = $this->buildPdfPageContent($lines, $marginLeft, $pageHeight - $marginTop, $lineHeight);
        }

        return $this->renderPdfDocument($pages, $pageWidth, $pageHeight);
    }

    protected function buildPdfPageContent(array $lines, int $startX, int $startY, int $lineHeight): string
    {
        $content = "BT\n/F1 9 Tf\n";

        foreach ($lines as $index => $line) {
            $y = $startY - ($index * $lineHeight);
            $content .= sprintf("1 0 0 1 %d %d Tm (%s) Tj\n", $startX, $y, $this->escapePdfText($line));
        }

        $content .= "ET";

        return $content;
    }

    protected function renderPdfDocument(array $pages, int $pageWidth, int $pageHeight): string
    {
        $objects = [];
        $pageObjectNumbers = [];
        $contentObjectNumbers = [];

        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';

        $objects[] = '<< /Type /Pages /Kids [' . implode(' ', array_map(function ($index) {
            return ($index * 2 + 3) . ' 0 R';
        }, array_keys($pages))) . '] /Count ' . count($pages) . ' >>';

        foreach ($pages as $pageIndex => $content) {
            $pageObjectNumber = count($objects) + 1;
            $contentObjectNumber = $pageObjectNumber + 1;
            $pageObjectNumbers[] = $pageObjectNumber;
            $contentObjectNumbers[] = $contentObjectNumber;

            $objects[] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %d %d] /Resources << /Font << /F1 %d 0 R >> >> /Contents %d 0 R >>',
                $pageWidth,
                $pageHeight,
                (count($pages) * 2) + 3,
                $contentObjectNumber
            );

            $objects[] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        }

        $fontObjectNumber = count($objects) + 1;
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    protected function escapePdfText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);

        return preg_replace('/[^\x20-\x7E]/', '?', $text) ?? $text;
    }

    protected function truncateText(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length - 3) . '...';
    }

    protected function formatRupiah(float|int|string|null $value): string
    {
        return 'Rp' . number_format((float) $value, 2, ',', '.');
    }
}
