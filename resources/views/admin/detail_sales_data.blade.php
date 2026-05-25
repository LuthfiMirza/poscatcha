@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Detail Sales Data</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin')}}">Home</a></li>
      <li class="breadcrumb-item active">Detail Sales Data</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Informasi Transaksi</h5>
              <div class="row">
                <div class="col-md-4">
                  <strong>No. Invoice:</strong><br>
                  {{ $saleHeader?->sale_id ?? '-' }}
                </div>
                <div class="col-md-4">
                  <strong>Total Transaksi:</strong><br>
                  @if ($saleHeader)
                    Rp{{ number_format($saleHeader->total, 2, ',', '.') }}
                  @else
                    -
                  @endif
                </div>
                <div class="col-md-4">
                  <strong>Tanggal:</strong><br>
                  {{ $saleHeader?->created_at?->format('d F Y H:i:s') ?? '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-12">
          <div class="card recent-sales overflow-auto">
            <div class="card-body">
              <h5 class="card-title">Detail Sales Data</h5>

              <table class="table table-borderless datatable">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Invoice</th>
                    <th scope="col">Product ID</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Sell Price</th>
                    <th scope="col">Buy Price</th>
                    <th scope="col">Sub Total</th>
                    <th scope="col">Profit</th>
                    <th scope="col">Action By</th>
                    <th scope="col">Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sales as $index => $sale)
                    <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>{{ $sale->sale_id }}</td>
                        <td>{{ $sale->product_id }}</td>
                        <td>{{ $sale->product_name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>Rp{{ number_format($sale->product_price, 2, ',', '.') }}</td>
                        <td>Rp{{ number_format($sale->buy_price ?? 0, 2, ',', '.') }}</td>
                        <td>Rp{{ number_format($sale->sub_total, 2, ',', '.') }}</td>
                        <td>Rp{{ number_format($sale->product_profit, 2, ',', '.') }}</td>
                        <td>
                          @foreach ($users as $user)
                            @if ($user->id == $sale->cashier_id)
                              {{ $user->name }}
                            @endif
                          @endforeach
                        </td>
                        <td>{{ date('d F Y', strtotime($sale->created_at)) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- End Recent Sales -->
    </div>
</section>
@endsection
