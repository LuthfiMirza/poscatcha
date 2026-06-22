@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Restock Bahan Baku</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Restock Bahan Baku</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  @include('admin.partials.flash')

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Filter Restock</h5>

      <form method="GET" action="{{ route('purchases.index') }}" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Tanggal Dari</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Sampai</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Supplier Terdaftar</label>
          <select name="supplier_id" class="form-select">
            <option value="">Semua Supplier</option>
            @foreach ($suppliers as $supplier)
              <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Supplier Manual</label>
          <input type="text" name="supplier_name" class="form-control" value="{{ request('supplier_name') }}" placeholder="Nama supplier">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Reset</a>
          <a href="{{ route('purchases.create') }}" class="btn btn-success">Tambah Restock</a>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Daftar Restock Bahan Baku</h5>

          <table class="table table-borderless datatable">
            <thead>
              <tr>
                <th>No</th>
                <th>Nomor Restock</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Invoice</th>
                <th>Total Qty</th>
                <th>Dibuat Oleh</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($purchases as $index => $purchase)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $purchase->purchase_number }}</td>
                  <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                  <td>{{ $purchase->supplier_label }}</td>
                  <td>{{ $purchase->invoice_number ?: '-' }}</td>
                  <td>{{ $purchase->items->sum('quantity') }}</td>
                  <td>{{ $purchase->creator?->name ?: '-' }}</td>
                  <td class="d-flex gap-1">
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-primary btn-sm">Detail</a>
                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" onsubmit="return confirm('Hapus restock ini dan kembalikan stok bahan?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                  </td>
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
