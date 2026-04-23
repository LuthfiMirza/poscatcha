@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Laporan Profit</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Laporan Profit</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  @include('admin.partials.flash')

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Filter Periode</h5>

      <form method="GET" action="{{ route('reports.profit') }}" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Tanggal Dari</label>
          <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Sampai</label>
          <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Urutkan Margin</label>
          <select name="sort" class="form-select">
            <option value="highest" @selected($sort === 'highest')>Margin Tertinggi</option>
            <option value="lowest" @selected($sort === 'lowest')>Margin Terendah</option>
          </select>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Tampilkan</button>
          <a href="{{ route('reports.profit') }}" class="btn btn-secondary">Reset</a>
          <a href="{{ route('reports.profit.export.excel', array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'sort' => $sort])) }}" class="btn btn-success">Export Excel</a>
          <a href="{{ route('reports.profit.export.pdf', array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'sort' => $sort])) }}" class="btn btn-danger">Export PDF</a>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-3 col-md-6">
      <div class="card info-card sales-card">
        <div class="card-body">
          <h5 class="card-title">Total Omzet</h5>
          <div class="fw-bold fs-5">Rp{{ number_format($summary['total_omzet'], 2, ',', '.') }}</div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card info-card revenue-card">
        <div class="card-body">
          <h5 class="card-title">Total Modal</h5>
          <div class="fw-bold fs-5">Rp{{ number_format($summary['total_modal'], 2, ',', '.') }}</div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card info-card customers-card">
        <div class="card-body">
          <h5 class="card-title">Laba Kotor</h5>
          <div class="fw-bold fs-5">Rp{{ number_format($summary['gross_profit'], 2, ',', '.') }}</div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Margin</h5>
          <div class="fw-bold fs-5">{{ number_format($summary['margin'], 2, ',', '.') }}%</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Profit per Produk</h5>
            <a href="{{ route('reports.profit', array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'sort' => $sort === 'highest' ? 'lowest' : 'highest'])) }}" class="btn btn-outline-primary btn-sm">
              {{ $sort === 'highest' ? 'Urutkan Margin Terendah' : 'Urutkan Margin Tertinggi' }}
            </a>
          </div>

          <table class="table table-borderless datatable">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Qty Terjual</th>
                <th>Omzet</th>
                <th>Modal</th>
                <th>Laba</th>
                <th>Margin</th>
              </tr>
            </thead>
            <tbody>
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
                  <td colspan="7" class="text-center">Belum ada data penjualan pada periode ini.</td>
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
