@extends('layouts.cashier')

@section('content')
<div class="pagetitle">
  <h1>Buka Shift</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item active">Buka Shift</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Mulai Shift Kasir</h5>
          <p class="text-muted mb-4">Masukkan kas awal sebelum memulai transaksi.</p>

          <form method="POST" action="{{ route('cashier.shift.store') }}">
            @csrf
            <div class="row mb-3">
              <label for="opening_cash" class="col-sm-3 col-form-label">Kas Awal</label>
              <div class="col-sm-9">
                <input type="number" step="0.01" min="0" class="form-control" id="opening_cash" name="opening_cash" value="{{ old('opening_cash', 0) }}" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="notes" class="col-sm-3 col-form-label">Catatan</label>
              <div class="col-sm-9">
                <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
              </div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary">Mulai Shift</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
