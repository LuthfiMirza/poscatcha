@extends('layouts.cashier')

@section('content')
<div class="pagetitle">
  <h1>Tutup Shift</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('list_product') }}">Home</a></li>
      <li class="breadcrumb-item active">Tutup Shift</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Ringkasan Shift Aktif</h5>
          <table class="table">
            <tr>
              <th>Kasir</th>
              <td>{{ Auth::user()->name }}</td>
            </tr>
            <tr>
              <th>Mulai Shift</th>
              <td>{{ $activeShift->shift_start->format('d M Y H:i') }}</td>
            </tr>
            <tr>
              <th>Kas Awal</th>
              <td>Rp{{ number_format($activeShift->opening_cash, 2, ',', '.') }}</td>
            </tr>
            <tr>
              <th>Total Transaksi</th>
              <td>{{ $summary['transactions_count'] }}</td>
            </tr>
            <tr>
              <th>Total Penjualan</th>
              <td>Rp{{ number_format($summary['total_sales'], 2, ',', '.') }}</td>
            </tr>
            <tr>
              <th>Cash</th>
              <td>Rp{{ number_format($summary['cash_total'], 2, ',', '.') }}</td>
            </tr>
            <tr>
              <th>QRIS</th>
              <td>Rp{{ number_format($summary['qris_total'], 2, ',', '.') }}</td>
            </tr>
            <tr>
              <th>Transfer</th>
              <td>Rp{{ number_format($summary['transfer_total'], 2, ',', '.') }}</td>
            </tr>
            <tr>
              <th>Kas Seharusnya</th>
              <td>Rp{{ number_format($summary['expected_cash'], 2, ',', '.') }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Form Tutup Shift</h5>

          <form method="POST" action="{{ route('cashier.shift.close.store') }}">
            @csrf
            <div class="row mb-3">
              <label for="closing_cash" class="col-sm-3 col-form-label">Kas Akhir Fisik</label>
              <div class="col-sm-9">
                <input type="number" step="0.01" min="0" class="form-control" id="closing_cash" name="closing_cash" value="{{ old('closing_cash') }}" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="notes" class="col-sm-3 col-form-label">Catatan</label>
              <div class="col-sm-9">
                <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes', $activeShift->notes) }}</textarea>
              </div>
            </div>

            <div class="text-end">
              <a href="{{ route('list_product') }}" class="btn btn-secondary">Kembali</a>
              <button type="submit" class="btn btn-danger">Tutup Shift</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
