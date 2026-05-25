@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Sales Data</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin')}}">Home</a></li>
      <li class="breadcrumb-item active">Sales Data</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

@include('admin.partials.flash')

<section class="section dashboard">
    <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Filter Sales Data</h5>

              <form method="GET" action="{{ route('sales_data') }}" class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Invoice</label>
                  <input type="text" name="sale_id" class="form-control" value="{{ request('sale_id') }}" placeholder="Cari invoice">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Metode Bayar</label>
                  <select name="payment_method" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" @selected(request('payment_method') === '1')>Cash</option>
                    <option value="2" @selected(request('payment_method') === '2')>Transfer</option>
                    <option value="3" @selected(request('payment_method') === '3')>QRIS</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Kasir</label>
                  <select name="cashier_id" class="form-select">
                    <option value="">Semua Kasir</option>
                    @foreach ($users as $user)
                      <option value="{{ $user->id }}" @selected((string) request('cashier_id') === (string) $user->id)>{{ $user->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Tanggal Dari</label>
                  <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Tanggal Sampai</label>
                  <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                  <a href="{{ route('sales_data') }}" class="btn btn-secondary">Reset</a>
                  <button type="submit" class="btn btn-primary">Filter</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-12">
          <div class="card recent-sales overflow-auto">
            <div class="card-body">
              <h5 class="card-title">Sales Data</h5>

              <table class="table table-borderless datatable">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Invoice</th>
                    <th scope="col">Total</th>
                    <th scope="col">Payment Method</th>
                    <th scope="col">Action By</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sales as $index => $sale)
                    <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>{{ $sale->sale_id }}</td>
                        <td>Rp{{ number_format($sale->total, 2, ',', '.') }}</td>
                        <td>
                          @if ($sale->payment_method == 1) Cash 
                          @elseif ($sale->payment_method == 2) Transfer 
                          @elseif ($sale->payment_method == 3) QRIS 
                          @endif
                        </td>
                        <td>
                          {{ $users->firstWhere('id', $sale->cashier_id)?->name ?? '-' }}
                        </td>
                        <td>{{ date('d F Y', strtotime($sale->created_at)) }}</td>
                        <td>
                          <a href="{{ route('detail_sales_data', $sale->sale_id)}}" class="btn badge bg-primary">Details</a>
                        </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

              @if ($sales->isEmpty())
                <div class="text-center text-muted py-4">Belum ada transaksi yang sesuai filter.</div>
              @endif
            </div>
          </div>
        </div><!-- End Recent Sales -->
    </div>
</section>
@endsection
