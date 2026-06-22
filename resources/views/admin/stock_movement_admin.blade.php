@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Stock Movement Bahan Baku</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Stock Movement Bahan Baku</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  @include('admin.partials.flash')

  <div class="alert alert-info">
    <strong>Alur stok sekarang:</strong> produk di kasir adalah menu minuman, sedangkan stok yang dilacak adalah bahan baku. Restock menambah stok bahan, penjualan mengurangi stok bahan sesuai resep produk.
  </div>

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Filter Stock Movement</h5>

      <form method="GET" action="{{ route('stock_movement') }}" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Tipe</label>
          <select name="type" class="form-select">
            <option value="">Semua Tipe</option>
            <option value="in" @selected(request('type') === 'in')>Masuk / Restock</option>
            <option value="out" @selected(request('type') === 'out')>Keluar / Penjualan</option>
            <option value="adjustment" @selected(request('type') === 'adjustment')>Koreksi</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Dari</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Sampai</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="{{ route('stock_movement') }}" class="btn btn-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Riwayat Stok Bahan Baku</h5>

          <table class="table table-borderless datatable align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Bahan</th>
                <th>Satuan</th>
                <th>Transaction ID</th>
                <th>Tipe</th>
                <th>Qty</th>
                <th>Stok Before</th>
                <th>Stok After</th>
                <th>Reason</th>
                <th>Action By</th>
                <th>Action Date</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($stock_movements as $index => $movement)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $movement->rawMaterial?->name ?: '-' }}</td>
                  <td>{{ $movement->rawMaterial?->unit ?: '-' }}</td>
                  <td>{{ $movement->transaction_id ?: '-' }}</td>
                  <td>
                    @if ($movement->type === 'in')
                      <span class="badge bg-success">Masuk</span>
                    @elseif ($movement->type === 'out')
                      <span class="badge bg-danger">Keluar</span>
                    @else
                      <span class="badge bg-warning text-dark">Koreksi</span>
                    @endif
                  </td>
                  <td>{{ number_format((float) $movement->quantity, 2, ',', '.') }}</td>
                  <td>{{ number_format((float) $movement->quantity_before, 2, ',', '.') }}</td>
                  <td>{{ number_format((float) $movement->quantity_after, 2, ',', '.') }}</td>
                  <td>{{ $movement->reason }}</td>
                  <td>{{ $movement->action_by }}</td>
                  <td>{{ $movement->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
