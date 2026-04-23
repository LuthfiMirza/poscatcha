@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Detail Supplier</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
      <li class="breadcrumb-item active">Detail Supplier</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  @include('admin.partials.flash')

  <div class="row">
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Informasi Supplier</h5>
          <table class="table">
            <tr>
              <th width="35%">Nama</th>
              <td>{{ $supplier->name }}</td>
            </tr>
            <tr>
              <th>Telepon</th>
              <td>{{ $supplier->phone ?: '-' }}</td>
            </tr>
            <tr>
              <th>Alamat</th>
              <td>{{ $supplier->address ?: '-' }}</td>
            </tr>
            <tr>
              <th>Dibuat</th>
              <td>{{ $supplier->created_at->format('d M Y H:i') }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Purchase Terakhir</h5>
          <table class="table table-borderless">
            <thead>
              <tr>
                <th>No</th>
                <th>Nomor Purchase</th>
                <th>Tanggal</th>
                <th>Invoice</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($supplier->purchases as $index => $purchase)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $purchase->purchase_number }}</td>
                  <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                  <td>{{ $purchase->invoice_number ?: '-' }}</td>
                  <td>
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-primary btn-sm">Detail</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center">Belum ada purchase untuk supplier ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
