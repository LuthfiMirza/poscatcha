@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Laporan Shift Kasir</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Shift Kasir</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  @include('admin.partials.flash')

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Filter Shift</h5>

      <form method="GET" action="{{ route('admin.shifts.index') }}" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Kasir</label>
          <select name="cashier_id" class="form-select">
            <option value="">Semua Kasir</option>
            @foreach ($cashiers as $cashier)
              <option value="{{ $cashier->id }}" @selected(request('cashier_id') == $cashier->id)>{{ $cashier->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Dari</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Sampai</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary">Reset</a>
          <a href="{{ route('admin.shifts.export.excel', array_filter(['cashier_id' => request('cashier_id'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}" class="btn btn-success">Export Excel</a>
          <a href="{{ route('admin.shifts.export.pdf', array_filter(['cashier_id' => request('cashier_id'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}" class="btn btn-danger">Export PDF</a>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Daftar Shift</h5>

          <table class="table table-borderless datatable">
            <thead>
              <tr>
                <th>No</th>
                <th>Kasir</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Status</th>
                <th>Kas Awal</th>
                <th>Cash</th>
                <th>QRIS</th>
                <th>Transfer</th>
                <th>Kas Seharusnya</th>
                <th>Kas Akhir</th>
                <th>Selisih</th>
                <th>Total Transaksi</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($shifts as $index => $shift)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $shift->cashier?->name ?: '-' }}</td>
                  <td>{{ $shift->shift_start->format('d M Y H:i') }}</td>
                  <td>{{ $shift->shift_end?->format('d M Y H:i') ?: '-' }}</td>
                  <td>
                    @if ($shift->status === 'open')
                      <span class="badge bg-success">Open</span>
                    @else
                      <span class="badge bg-secondary">Closed</span>
                    @endif
                  </td>
                  <td>Rp{{ number_format($shift->opening_cash, 2, ',', '.') }}</td>
                  <td>Rp{{ number_format($shift->cash_total, 2, ',', '.') }}</td>
                  <td>Rp{{ number_format($shift->qris_total, 2, ',', '.') }}</td>
                  <td>Rp{{ number_format($shift->transfer_total, 2, ',', '.') }}</td>
                  <td>Rp{{ number_format($shift->expected_cash, 2, ',', '.') }}</td>
                  <td>
                    @if ($shift->closing_cash !== null)
                      Rp{{ number_format($shift->closing_cash, 2, ',', '.') }}
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    @if ($shift->difference !== null)
                      Rp{{ number_format($shift->difference, 2, ',', '.') }}
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $shift->transactions_count }}</td>
                  <td>{{ $shift->notes ?: '-' }}</td>
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
