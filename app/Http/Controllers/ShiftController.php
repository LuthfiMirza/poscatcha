<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.shifts.index', $this->buildShiftReport($request));
    }

    public function exportExcel(Request $request): Response
    {
        $report = $this->buildShiftReport($request);
        $filename = 'laporan-shift-' . now()->format('Ymd-His') . '.xls';
        $content = view('admin.shifts.export_excel', $report)->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $report = $this->buildShiftReport($request);
        $filename = 'laporan-shift-' . now()->format('Ymd-His') . '.pdf';
        $pdf = $this->buildSimplePdf($report);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function openForm(): View|RedirectResponse
    {
        $activeShift = $this->getActiveShift();

        if ($activeShift) {
            return redirect()
                ->route('selling_product')
                ->with('success', 'Shift Anda masih aktif.');
        }

        return view('cashier.shifts.open');
    }

    public function open(Request $request): RedirectResponse
    {
        $activeShift = $this->getActiveShift();

        if ($activeShift) {
            return redirect()
                ->route('selling_product')
                ->with('success', 'Shift Anda masih aktif.');
        }

        $validated = $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        CashierShift::create([
            'cashier_id' => Auth::id(),
            'shift_start' => now(),
            'opening_cash' => $validated['opening_cash'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'open',
        ]);

        return redirect()
            ->route('selling_product')
            ->with('success', 'Shift berhasil dibuka.');
    }

    public function closeForm(): View|RedirectResponse
    {
        $activeShift = $this->getActiveShift();

        if (!$activeShift) {
            return redirect()
                ->route('cashier.shift.open')
                ->with('error', 'Belum ada shift aktif untuk ditutup.');
        }

        $summary = $this->buildShiftSummary($activeShift);

        return view('cashier.shifts.close', compact('activeShift', 'summary'));
    }

    public function close(Request $request): RedirectResponse
    {
        $activeShift = $this->getActiveShift();

        if (!$activeShift) {
            return redirect()
                ->route('cashier.shift.open')
                ->with('error', 'Belum ada shift aktif untuk ditutup.');
        }

        $validated = $request->validate([
            'closing_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $summary = $this->buildShiftSummary($activeShift);

        $activeShift->update([
            'shift_end' => now(),
            'closing_cash' => $validated['closing_cash'],
            'notes' => $validated['notes'] ?? $activeShift->notes,
            'status' => 'closed',
        ]);

        $difference = (float) $validated['closing_cash'] - $summary['expected_cash'];

        return redirect()
            ->route('cashier.shift.open')
            ->with('success', 'Shift berhasil ditutup. Selisih kas: Rp' . number_format($difference, 2, ',', '.'));
    }

    public static function activeShiftForCashier(?int $cashierId): ?CashierShift
    {
        if (!$cashierId) {
            return null;
        }

        return CashierShift::query()
            ->open()
            ->where('cashier_id', $cashierId)
            ->first();
    }

    public static function buildHeaderShiftInfo(?int $cashierId): ?array
    {
        $shift = static::activeShiftForCashier($cashierId);

        if (!$shift) {
            return null;
        }

        $transactionsCount = Sale::query()
            ->where('shift_id', $shift->id)
            ->count();

        return [
            'shift' => $shift,
            'transactions_count' => $transactionsCount,
        ];
    }

    protected function getActiveShift(): ?CashierShift
    {
        return static::activeShiftForCashier(Auth::id());
    }

    protected function buildShiftReport(Request $request): array
    {
        $cashiers = User::role('cashier')
            ->orderBy('name')
            ->get();

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $cashierId = $request->input('cashier_id');

        $shifts = CashierShift::query()
            ->with(['cashier', 'sales'])
            ->when($cashierId, function ($query) use ($cashierId) {
                $query->where('cashier_id', $cashierId);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('shift_start', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('shift_start', '<=', $dateTo);
            })
            ->latest('shift_start')
            ->get();

        $shifts = $this->hydrateShiftMetrics($shifts);

        return [
            'cashiers' => $cashiers,
            'shifts' => $shifts,
            'periodLabel' => $this->buildPeriodLabel($dateFrom, $dateTo),
            'selectedCashier' => $cashiers->firstWhere('id', (int) $cashierId),
            'printedBy' => Auth::user()?->name ?? 'System',
            'printedAt' => now(),
            'store' => $this->buildStoreProfile(),
            'summary' => [
                'total_shifts' => $shifts->count(),
                'open_shifts' => $shifts->where('status', 'open')->count(),
                'closed_shifts' => $shifts->where('status', 'closed')->count(),
                'total_cash' => (float) $shifts->sum('cash_total'),
                'total_qris' => (float) $shifts->sum('qris_total'),
                'total_transfer' => (float) $shifts->sum('transfer_total'),
                'total_difference' => (float) $shifts->sum(function ($shift) {
                    return (float) ($shift->difference ?? 0);
                }),
                'total_transactions' => (int) $shifts->sum('transactions_count'),
            ],
        ];
    }

    protected function buildShiftSummary(CashierShift $shift): array
    {
        $sales = Sale::query()
            ->where('shift_id', $shift->id)
            ->get();

        $cashTotal = (float) $sales->where('payment_method', '1')->sum('total');
        $qrisTotal = (float) $sales->where('payment_method', '3')->sum('total');
        $transferTotal = (float) $sales->where('payment_method', '2')->sum('total');
        $expectedCash = (float) $shift->opening_cash + $cashTotal;

        return [
            'transactions_count' => $sales->count(),
            'total_sales' => (float) $sales->sum('total'),
            'cash_total' => $cashTotal,
            'qris_total' => $qrisTotal,
            'transfer_total' => $transferTotal,
            'expected_cash' => $expectedCash,
        ];
    }

    protected function hydrateShiftMetrics(Collection $shifts): Collection
    {
        return $shifts->map(function (CashierShift $shift) {
            $shift->setAttribute('cash_total', (float) $shift->sales->where('payment_method', '1')->sum('total'));
            $shift->setAttribute('qris_total', (float) $shift->sales->where('payment_method', '3')->sum('total'));
            $shift->setAttribute('transfer_total', (float) $shift->sales->where('payment_method', '2')->sum('total'));
            $shift->setAttribute('transactions_count', $shift->sales->count());
            $shift->setAttribute('total_sales_amount', (float) $shift->sales->sum('total'));

            $expectedCash = (float) $shift->opening_cash + (float) $shift->getAttribute('cash_total');
            $difference = $shift->closing_cash !== null
                ? (float) $shift->closing_cash - $expectedCash
                : null;

            $shift->setAttribute('expected_cash', $expectedCash);
            $shift->setAttribute('difference', $difference);

            return $shift;
        });
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
        $marginLeft = 24;
        $marginTop = 32;
        $lineHeight = 14;
        $rowsPerPage = 16;

        $cashierLabel = $report['selectedCashier']?->name ?? 'Semua kasir';
        $store = $report['store'];

        $headerLines = [
            $this->centerText($store['name'], 118),
            $this->centerText($store['address'], 118),
            $this->centerText('Telp: ' . $store['phone'], 118),
            str_repeat('=', 118),
            $this->centerText('LAPORAN SHIFT KASIR / KAS OPNAME', 118),
            str_repeat('=', 118),
            'Periode        : ' . $report['periodLabel'],
            'Kasir          : ' . $cashierLabel,
            'Dicetak Oleh   : ' . $report['printedBy'],
            'Waktu Cetak    : ' . $report['printedAt']->format('d/m/Y H:i:s'),
            '',
            str_pad('No', 4)
                . str_pad('Kasir', 18)
                . str_pad('Mulai', 18)
                . str_pad('Status', 8)
                . str_pad('Awal', 14, ' ', STR_PAD_LEFT)
                . str_pad('Cash', 14, ' ', STR_PAD_LEFT)
                . str_pad('QRIS', 14, ' ', STR_PAD_LEFT)
                . str_pad('Transfer', 14, ' ', STR_PAD_LEFT)
                . str_pad('Selisih', 14, ' ', STR_PAD_LEFT),
            str_repeat('-', 118),
        ];

        $bodyLines = [];

        foreach ($report['shifts'] as $index => $shift) {
            $bodyLines[] =
                str_pad((string) ($index + 1), 4)
                . str_pad($this->truncateText($shift->cashier?->name ?: '-', 17), 18)
                . str_pad($shift->shift_start->format('d/m H:i'), 18)
                . str_pad(strtoupper($shift->status), 8)
                . str_pad($this->formatRupiahShort($shift->opening_cash), 14, ' ', STR_PAD_LEFT)
                . str_pad($this->formatRupiahShort($shift->cash_total), 14, ' ', STR_PAD_LEFT)
                . str_pad($this->formatRupiahShort($shift->qris_total), 14, ' ', STR_PAD_LEFT)
                . str_pad($this->formatRupiahShort($shift->transfer_total), 14, ' ', STR_PAD_LEFT)
                . str_pad($this->formatRupiahShort($shift->difference ?? 0), 14, ' ', STR_PAD_LEFT);
        }

        if (empty($bodyLines)) {
            $bodyLines[] = 'Belum ada data shift pada filter ini.';
        }

        $chunks = array_chunk($bodyLines, $rowsPerPage);
        $pages = [];
        $summaryLines = [
            '',
            str_repeat('-', 118),
            'RINGKASAN AUDIT',
            str_repeat('-', 118),
            'Total Shift         : ' . $report['summary']['total_shifts'] . ' shift',
            'Shift Closed        : ' . $report['summary']['closed_shifts'] . ' shift',
            'Shift Open          : ' . $report['summary']['open_shifts'] . ' shift',
            'Total Transaksi     : ' . $report['summary']['total_transactions'] . ' transaksi',
            'Total Cash          : ' . $this->formatRupiah($report['summary']['total_cash']),
            'Total QRIS          : ' . $this->formatRupiah($report['summary']['total_qris']),
            'Total Transfer      : ' . $this->formatRupiah($report['summary']['total_transfer']),
            'Akumulasi Selisih   : ' . $this->formatRupiah($report['summary']['total_difference']),
            '',
            'Catatan Audit: laporan ini dicetak sebagai dokumen kontrol shift kasir dan pemeriksaan kas harian.',
            '',
            str_pad('Disetujui Admin', 45) . str_pad('Diketahui Kasir', 45),
            '',
            '',
            '',
            str_pad('(____________________)', 45) . str_pad('(____________________)', 45),
        ];

        foreach ($chunks as $pageIndex => $chunk) {
            $lines = $headerLines;

            if ($pageIndex > 0) {
                $lines[4] = $this->centerText('LAPORAN SHIFT KASIR / KAS OPNAME (LANJUTAN)', 118);
            }

            $lines = array_merge($lines, $chunk);
            $pages[] = $this->buildPdfPageContent($lines, $marginLeft, $pageHeight - $marginTop, $lineHeight);
        }

        $summaryHeaderLines = [
            $this->centerText($store['name'], 118),
            $this->centerText($store['address'], 118),
            $this->centerText('Telp: ' . $store['phone'], 118),
            str_repeat('=', 118),
            $this->centerText('LEMBAR PENGESAHAN AUDIT SHIFT KASIR', 118),
            str_repeat('=', 118),
            'Periode        : ' . $report['periodLabel'],
            'Kasir          : ' . $cashierLabel,
            'Dicetak Oleh   : ' . $report['printedBy'],
            'Waktu Cetak    : ' . $report['printedAt']->format('d/m/Y H:i:s'),
            '',
        ];

        $pages[] = $this->buildPdfPageContent(
            array_merge($summaryHeaderLines, $summaryLines),
            $marginLeft,
            $pageHeight - $marginTop,
            $lineHeight
        );

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

        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';

        $objects[] = '<< /Type /Pages /Kids [' . implode(' ', array_map(function ($index) {
            return ($index * 2 + 3) . ' 0 R';
        }, array_keys($pages))) . '] /Count ' . count($pages) . ' >>';

        foreach ($pages as $content) {
            $pageObjectNumber = count($objects) + 1;
            $contentObjectNumber = $pageObjectNumber + 1;

            $objects[] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %d %d] /Resources << /Font << /F1 %d 0 R >> >> /Contents %d 0 R >>',
                $pageWidth,
                $pageHeight,
                (count($pages) * 2) + 3,
                $contentObjectNumber
            );

            $objects[] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        }

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

    protected function formatRupiahShort(float|int|string|null $value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }

    protected function formatRupiah(float|int|string|null $value): string
    {
        return 'Rp' . number_format((float) $value, 2, ',', '.');
    }

    protected function centerText(string $text, int $width): string
    {
        $textLength = mb_strlen($text);

        if ($textLength >= $width) {
            return $text;
        }

        $leftPadding = (int) floor(($width - $textLength) / 2);

        return str_repeat(' ', $leftPadding) . $text;
    }

    protected function buildStoreProfile(): array
    {
        return [
            'name' => 'Catcha',
            'address' => 'Kecamatan Ciputat, Kota Tangerang Selatan',
            'phone' => '0812-3456-7890',
        ];
    }
}
