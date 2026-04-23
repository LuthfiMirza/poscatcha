<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Profit</title>
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="7" style="font-size:16px;">Laporan Profit</th>
        </tr>
        <tr>
            <td colspan="7">Periode: {{ $periodLabel }}</td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <th>Total Omzet</th>
            <th>Total Modal</th>
            <th>Laba Kotor</th>
            <th>Margin (%)</th>
            <th colspan="3"></th>
        </tr>
        <tr>
            <td>Rp{{ number_format($summary['total_omzet'], 2, ',', '.') }}</td>
            <td>Rp{{ number_format($summary['total_modal'], 2, ',', '.') }}</td>
            <td>Rp{{ number_format($summary['gross_profit'], 2, ',', '.') }}</td>
            <td>{{ number_format($summary['margin'], 2, ',', '.') }}%</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Qty Terjual</th>
            <th>Omzet</th>
            <th>Modal</th>
            <th>Laba</th>
            <th>Margin (%)</th>
        </tr>
        @forelse ($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ number_format($product->qty_sold, 0, ',', '.') }}</td>
                <td>Rp{{ number_format($product->omzet, 2, ',', '.') }}</td>
                <td>Rp{{ number_format($product->modal, 2, ',', '.') }}</td>
                <td>Rp{{ number_format($product->laba, 2, ',', '.') }}</td>
                <td>{{ number_format($product->margin_percent, 2, ',', '.') }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="7">Belum ada data penjualan pada periode ini.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>
