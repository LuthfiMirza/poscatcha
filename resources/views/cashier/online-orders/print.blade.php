<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->order_code }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0 auto;
            max-width: 300px;
            padding: 10px;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 3px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            font-size: 11px;
            padding: 2px 0;
            text-align: left;
            vertical-align: top;
        }

        th {
            border-bottom: 1px solid #000;
        }

        .total {
            border-top: 1px solid #000;
            font-size: 14px;
            font-weight: bold;
            padding-top: 5px;
        }

        .no-print {
            margin-top: 16px;
            text-align: center;
        }

        .print-btn {
            background: #e8650a;
            border: 0;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            font-weight: bold;
            padding: 9px 14px;
        }

        @media print {
            body { padding: 5px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="center">
        <div class="store-name">CATcha</div>
        <div>Online Pickup Order</div>
        <div>Terima kasih atas pesanan Anda</div>
    </div>

    <div class="divider"></div>

    <div class="row"><span>Kode</span><span class="bold">{{ $order->order_code }}</span></div>
    <div class="row"><span>Tanggal</span><span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Buyer</span><span>{{ $order->buyer?->name ?: '-' }}</span></div>
    <div class="row"><span>Status</span><span>{{ $order->statusLabel() }}</span></div>
    <div class="row"><span>Bayar</span><span>{{ $order->paymentMethodLabel() }} / {{ $order->paymentStatusLabel() }}</span></div>

    @if ($order->note)
        <div class="divider"></div>
        <div class="bold">Catatan:</div>
        <div>{{ $order->note }}</div>
    @endif

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="center">Qty</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if ($item->customizationSummary())
                            <br><small>{{ $item->customizationSummary() }}</small>
                        @endif
                        <br><small>@ Rp{{ number_format($item->price, 0, ',', '.') }}</small>
                    </td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="row total"><span>Total</span><span>Rp{{ number_format($order->total_price, 0, ',', '.') }}</span></div>

    <div class="divider"></div>

    <div class="center">
        Simpan struk ini sebagai bukti order pickup.<br>
        Silakan tunjukkan ke kasir saat pengambilan.
    </div>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">Print Struk</button>
    </div>
</body>
</html>
