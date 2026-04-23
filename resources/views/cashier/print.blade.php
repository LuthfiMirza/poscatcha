<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .store-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .store-info {
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .transaction-info {
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .transaction-info div {
            margin-bottom: 2px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .items-table th,
        .items-table td {
            text-align: left;
            padding: 2px 0;
            font-size: 11px;
        }
        
        .items-table th {
            border-bottom: 1px solid #000;
            font-weight: bold;
        }
        
        .item-name {
            width: 50%;
        }
        
        .item-qty {
            width: 15%;
            text-align: center;
        }
        
        .item-price {
            width: 20%;
            text-align: right;
        }
        
        .item-total {
            width: 20%;
            text-align: right;
        }
        
        .total-section {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 10px;
        }
        
        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .total-line.grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .payment-method {
            text-transform: capitalize;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 5px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="store-name">{{ $store_name }}</div>
            <div class="store-info">{{ $store_address }}</div>
            <div class="store-info">Telp: {{ $store_phone }}</div>
        </div>
        
        <!-- Transaction Info -->
        <div class="transaction-info">
            <div><strong>No. Invoice:</strong> {{ $sale->sale_id }}</div>
            <div><strong>Tanggal:</strong> {{ $sale->created_at->format('d/m/Y H:i:s') }}</div>
            <div><strong>Kasir:</strong> {{ $cashier_name }}</div>
        </div>
        
        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="item-name">Item</th>
                    <th class="item-qty">Qty</th>
                    <th class="item-price">Harga</th>
                    <th class="item-total">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $detail)
                <tr>
                    <td class="item-name">{{ $detail->product_name }}</td>
                    <td class="item-qty">{{ $detail->quantity }}</td>
                    <td class="item-price">{{ number_format($detail->product_price, 0, ',', '.') }}</td>
                    <td class="item-total">{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Total Section -->
        <div class="total-section">
            <div class="total-line">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>
            <div class="total-line grand-total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>
            <div class="total-line">
                <span>Metode Bayar:</span>
                <span class="payment-method">
                    @switch($sale->payment_method)
                        @case(1)
                            Cash
                            @break
                        @case(2)
                            Transfer
                            @break
                        @case(3)
                            QRIS
                            @break
                        @default
                            Unknown
                    @endswitch
                </span>
            </div>
            <div class="total-line">
                <span>Bayar:</span>
                <span>Rp {{ number_format($sale->pay, 0, ',', '.') }}</span>
            </div>
            <div class="total-line">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($sale->change, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div>Terima kasih atas kunjungan Anda!</div>
            <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
            <div>{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
        
        // Close window after printing
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>
</html>
