@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Detail Purchase</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
      <li class="breadcrumb-item active">Detail Purchase</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  @include('admin.partials.flash')

  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Informasi Purchase</h5>
          <table class="table">
            <tr>
              <th width="40%">Nomor</th>
              <td>{{ $purchase->purchase_number }}</td>
            </tr>
            <tr>
              <th>Tanggal</th>
              <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
            </tr>
            <tr>
              <th>Supplier</th>
              <td>{{ $purchase->supplier_label }}</td>
            </tr>
            <tr>
              <th>Invoice</th>
              <td>{{ $purchase->invoice_number ?: '-' }}</td>
            </tr>
            <tr>
              <th>Dibuat Oleh</th>
              <td>{{ $purchase->creator?->name ?: '-' }}</td>
            </tr>
            <tr>
              <th>Catatan</th>
              <td>{{ $purchase->notes ?: '-' }}</td>
            </tr>
          </table>

          <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" onsubmit="return confirm('Hapus purchase ini dan kembalikan stok?')">
            @csrf
            @method('DELETE')
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning">Edit Purchase</a>
            <button type="submit" class="btn btn-danger">Hapus Purchase</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Item Purchase</h5>
          <table class="table table-borderless">
            <thead>
              <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Harga Beli</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @php $grandTotal = 0; @endphp
              @foreach ($purchase->items as $index => $item)
                @php $subtotal = $item->quantity * $item->buy_price; $grandTotal += $subtotal; @endphp
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $item->product_id }}</td>
                  <td>{{ $item->product?->product_name ?: '-' }}</td>
                  <td>{{ $item->quantity }}</td>
                  <td>Rp{{ number_format($item->buy_price, 2, ',', '.') }}</td>
                  <td>Rp{{ number_format($subtotal, 2, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th colspan="5" class="text-end">Grand Total</th>
                <th>Rp{{ number_format($grandTotal, 2, ',', '.') }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
