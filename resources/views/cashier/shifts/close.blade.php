@extends('layouts.cashier')

@section('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f8f8f8;
      font-family: "Inter", sans-serif;
    }

    #header,
    #sidebar,
    .footer,
    .back-to-top {
      display: none !important;
    }

    #main.main {
      margin: 0 !important;
      padding: 32px 16px !important;
      min-height: 100vh;
      background: #f8f8f8;
    }

    .cashier-shift-page {
      display: flex;
      align-items: flex-start;
      justify-content: center;
      min-height: calc(100vh - 64px);
      padding-top: 48px;
    }

    .cashier-shift-card {
      width: 100%;
      max-width: 420px;
      border: 1px solid #f0f0f0;
      border-radius: 16px;
      background: #ffffff;
      padding: 32px;
      box-shadow: none;
    }

    .cashier-shift-brand {
      text-align: center;
    }

    .cashier-shift-logo {
      width: 48px;
      height: 48px;
      margin: 0 auto;
      border-radius: 14px;
      background: #6aaa2a;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      line-height: 1;
    }

    .cashier-shift-logo__cat {
      color: #ffffff;
      font-size: 12px;
      font-weight: 700;
    }

    .cashier-shift-logo__cha {
      color: #e8650a;
      font-size: 12px;
      font-weight: 700;
    }

    .cashier-shift-brand__name {
      margin-top: 12px;
      color: #111827;
      font-size: 16px;
      font-weight: 600;
    }

    .cashier-shift-title {
      margin-top: 24px;
      color: #111827;
      font-size: 20px;
      font-weight: 600;
      text-align: center;
      line-height: 1.2;
    }

    .cashier-shift-subtitle {
      margin-top: 4px;
      color: #9ca3af;
      font-size: 13px;
      text-align: center;
    }

    .cashier-shift-summary {
      margin-top: 24px;
      padding: 20px;
      border-radius: 14px;
      background: #f8f8f8;
    }

    .cashier-shift-summary__row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
    }

    .cashier-shift-summary__row:last-child {
      margin-bottom: 0;
    }

    .cashier-shift-summary__row span:first-child {
      color: #6b7280;
      font-size: 12px;
    }

    .cashier-shift-summary__row span:last-child {
      color: #111827;
      font-size: 13px;
      font-weight: 600;
      text-align: right;
    }

    .cashier-shift-summary__divider {
      margin: 12px 0;
      border: 0;
      border-top: 1px solid #f0f0f0;
      opacity: 1;
    }

    .cashier-shift-summary__row.is-total span:first-child {
      color: #111827;
      font-size: 13px;
      font-weight: 600;
    }

    .cashier-shift-summary__row.is-total span:last-child {
      color: #e8650a;
      font-size: 18px;
      font-weight: 700;
    }

    .cashier-shift-warning {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-top: 16px;
      padding: 12px 14px;
      border: 1px solid #fee2e2;
      border-radius: 14px;
      background: #fef2f2;
      color: #ef4444;
      font-size: 12px;
      line-height: 1.5;
    }

    .cashier-shift-warning i {
      font-size: 16px;
      color: #f87171;
      flex: 0 0 16px;
      margin-top: 1px;
    }

    .cashier-shift-form {
      margin-top: 24px;
    }

    .cashier-shift-field {
      margin-bottom: 16px;
    }

    .cashier-shift-field:last-of-type {
      margin-bottom: 0;
    }

    .cashier-shift-field label {
      display: block;
      margin-bottom: 6px;
      color: #374151;
      font-size: 12px;
      font-weight: 500;
    }

    .cashier-shift-field .form-control {
      min-height: 48px;
      border: 1px solid #f0f0f0;
      border-radius: 14px;
      background: #f8f8f8;
      color: #111827;
      font-size: 13px;
      padding: 12px 16px;
      box-shadow: none;
    }

    .cashier-shift-field .form-control:focus {
      border-color: #e8650a;
      background: #f8f8f8;
      box-shadow: none;
    }

    .cashier-shift-field textarea.form-control {
      min-height: 112px;
      resize: vertical;
    }

    .cashier-shift-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    .cashier-shift-back,
    .cashier-shift-submit {
      flex: 1 1 0;
      min-height: 48px;
      border-radius: 14px;
      font-size: 13px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: border-color 160ms ease, color 160ms ease, background-color 160ms ease;
    }

    .cashier-shift-back {
      border: 1px solid #f0f0f0;
      background: #ffffff;
      color: #6b7280;
    }

    .cashier-shift-back:hover,
    .cashier-shift-back:focus {
      border-color: #e8650a;
      color: #e8650a;
    }

    .cashier-shift-submit {
      border: 1px solid #e8650a;
      background: #e8650a;
      color: #ffffff;
    }

    .cashier-shift-submit:hover,
    .cashier-shift-submit:focus {
      background: #c85508;
      border-color: #c85508;
      color: #ffffff;
    }
  </style>
@endsection

@section('content')
<div class="cashier-shift-page">
  <div class="cashier-shift-card">
    <div class="cashier-shift-brand">
      <div class="cashier-shift-logo">
        <span class="cashier-shift-logo__cat">CAT</span>
        <span class="cashier-shift-logo__cha">cha</span>
      </div>
      <div class="cashier-shift-brand__name">CATcha POS</div>
    </div>

    <div class="cashier-shift-title">Tutup shift</div>
    <div class="cashier-shift-subtitle">Ringkasan sesi kasir</div>

    <div class="cashier-shift-summary">
      <div class="cashier-shift-summary__row">
        <span>Kasir</span>
        <span>{{ Auth::user()->name }}</span>
      </div>
      <div class="cashier-shift-summary__row">
        <span>Mulai shift</span>
        <span>{{ $activeShift->shift_start->format('d M Y H:i') }}</span>
      </div>
      <div class="cashier-shift-summary__row">
        <span>Kas Awal</span>
        <span>Rp{{ number_format($activeShift->opening_cash, 2, ',', '.') }}</span>
      </div>
      <div class="cashier-shift-summary__row">
        <span>Total Transaksi</span>
        <span>{{ $summary['transactions_count'] }}</span>
      </div>
      <div class="cashier-shift-summary__row">
        <span>Cash</span>
        <span>Rp{{ number_format($summary['cash_total'], 2, ',', '.') }}</span>
      </div>
      <div class="cashier-shift-summary__row">
        <span>QRIS</span>
        <span>Rp{{ number_format($summary['qris_total'], 2, ',', '.') }}</span>
      </div>
      <div class="cashier-shift-summary__row">
        <span>Transfer</span>
        <span>Rp{{ number_format($summary['transfer_total'], 2, ',', '.') }}</span>
      </div>
      <div class="cashier-shift-summary__row">
        <span>Kas Seharusnya</span>
        <span>Rp{{ number_format($summary['expected_cash'], 2, ',', '.') }}</span>
      </div>

      <hr class="cashier-shift-summary__divider">

      <div class="cashier-shift-summary__row is-total">
        <span>Total penjualan</span>
        <span>Rp{{ number_format($summary['total_sales'], 2, ',', '.') }}</span>
      </div>
    </div>

    <div class="cashier-shift-warning">
      <i class="bi bi-exclamation-triangle"></i>
      <span>Pastikan semua transaksi sudah selesai sebelum menutup shift.</span>
    </div>

    <form method="POST" action="{{ route('cashier.shift.close.store') }}" class="cashier-shift-form">
      @csrf
      <div class="cashier-shift-field">
        <label for="closing_cash">Kas Akhir Fisik</label>
        <input type="number" step="0.01" min="0" class="form-control" id="closing_cash" name="closing_cash" value="{{ old('closing_cash') }}" required>
      </div>

      <div class="cashier-shift-field">
        <label for="notes">Catatan</label>
        <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes', $activeShift->notes) }}</textarea>
      </div>

      <div class="cashier-shift-actions">
        <a href="{{ route('selling_product') }}" class="cashier-shift-back">Batal</a>
        <button type="submit" class="cashier-shift-submit">Tutup Shift</button>
      </div>
    </form>
  </div>
</div>
@endsection
