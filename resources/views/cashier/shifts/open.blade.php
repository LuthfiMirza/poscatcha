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
      letter-spacing: 0.02em;
    }

    .cashier-shift-logo__cha {
      color: #e8650a;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.02em;
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

    .cashier-shift-info {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-top: 24px;
      padding: 16px;
      border: 1px solid #ffedd5;
      border-radius: 14px;
      background: #fff7ed;
    }

    .cashier-shift-info__icon {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: rgba(232, 101, 10, 0.12);
      color: #e8650a;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex: 0 0 36px;
    }

    .cashier-shift-info__title {
      color: #1f2937;
      font-size: 13px;
      font-weight: 500;
    }

    .cashier-shift-info__meta {
      margin-top: 2px;
      color: #9ca3af;
      font-size: 11px;
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

    .cashier-shift-submit {
      width: 100%;
      margin-top: 24px;
      border: 1px solid #e8650a;
      border-radius: 14px;
      background: #e8650a;
      color: #ffffff;
      font-size: 14px;
      font-weight: 600;
      padding: 14px 16px;
      transition: background-color 160ms ease, border-color 160ms ease;
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

    <div class="cashier-shift-title">Buka shift baru</div>
    <div class="cashier-shift-subtitle">Mulai sesi kasir hari ini</div>

    <div class="cashier-shift-info">
      <div class="cashier-shift-info__icon">
        <i class="bi bi-clock-history"></i>
      </div>
      <div>
        <div class="cashier-shift-info__title">{{ Auth::user()->name }}</div>
        <div class="cashier-shift-info__meta">{{ now()->format('d M Y') }} · <span id="clock-now">00:00:00 WIB</span></div>
      </div>
    </div>

    <form method="POST" action="{{ route('cashier.shift.store') }}" class="cashier-shift-form">
      @csrf
      <div class="cashier-shift-field">
        <label for="opening_cash">Kas Awal</label>
        <input type="number" step="0.01" min="0" class="form-control" id="opening_cash" name="opening_cash" value="{{ old('opening_cash', 0) }}" required>
      </div>

      <div class="cashier-shift-field">
        <label for="notes">Catatan</label>
        <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
      </div>

      <button type="submit" class="cashier-shift-submit">Buka Shift</button>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
  function pad(n){ return String(n).padStart(2,'0'); }
  function tick(){
    const el = document.getElementById('clock-now');
    if(!el) return;
    const wib = new Date(new Date().toLocaleString('en-US',
      {timeZone:'Asia/Jakarta'}));
    el.textContent = pad(wib.getHours())+':'+pad(wib.getMinutes())
      +':'+pad(wib.getSeconds())+' WIB';
  }
  tick(); setInterval(tick,1000);
})();
</script>
@endsection
