<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Shift Kasir</title>
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="14" style="font-size:16px;">Laporan Shift Kasir</th>
        </tr>
        <tr>
            <td colspan="14">Periode: {{ $periodLabel }}</td>
        </tr>
        <tr>
            <td colspan="14">Kasir: {{ $selectedCashier?->name ?? 'Semua kasir' }}</td>
        </tr>
        <tr>
            <td colspan="14"></td>
        </tr>
        <tr>
            <th>No</th>
            <th>Kasir</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Status</th>
            <th>Kas Awal</th>
            <th>Cash</th>
            <th>QRIS</th>
            <th>Transfer</th>
            <th>Kas Seharusnya</th>
            <th>Kas Akhir</th>
            <th>Selisih</th>
            <th>Total Transaksi</th>
            <th>Catatan</th>
        </tr>
        @forelse ($shifts as $index => $shift)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $shift->cashier?->name ?: '-' }}</td>
                <td>{{ $shift->shift_start->format('d/m/Y H:i') }}</td>
                <td>{{ $shift->shift_end?->format('d/m/Y H:i') ?: '-' }}</td>
                <td>{{ strtoupper($shift->status) }}</td>
                <td>Rp{{ number_format($shift->opening_cash, 2, ',', '.') }}</td>
                <td>Rp{{ number_format($shift->cash_total, 2, ',', '.') }}</td>
                <td>Rp{{ number_format($shift->qris_total, 2, ',', '.') }}</td>
                <td>Rp{{ number_format($shift->transfer_total, 2, ',', '.') }}</td>
                <td>Rp{{ number_format($shift->expected_cash, 2, ',', '.') }}</td>
                <td>
                    @if ($shift->closing_cash !== null)
                        Rp{{ number_format($shift->closing_cash, 2, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ($shift->difference !== null)
                        Rp{{ number_format($shift->difference, 2, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $shift->transactions_count }}</td>
                <td>{{ $shift->notes ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="14">Belum ada data shift pada filter ini.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>
