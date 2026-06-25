@extends('layouts.cashier')

@section('page-title', 'Pesanan Online')

@section('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #ffffff;
      font-family: "Inter", sans-serif;
    }

    #main.main {
      margin-top: 56px !important;
      margin-left: 60px !important;
      padding: 0 !important;
      min-height: calc(100vh - 56px);
      background: #ffffff;
    }

    .online-orders-page {
      min-height: calc(100vh - 56px);
      padding: 24px 32px;
      background: #ffffff;
    }

    .online-orders-heading {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 24px;
    }

    .online-orders-heading__title {
      margin: 0;
      color: #111827;
      font-size: 20px;
      font-weight: 600;
      line-height: 1.2;
    }

    .online-orders-heading__breadcrumb {
      margin-top: 4px;
      color: #9ca3af;
      font-size: 12px;
      line-height: 1.4;
    }

    .online-orders-filter {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 220px 120px;
      gap: 10px;
      margin-bottom: 18px;
      padding: 16px;
      border: 1px solid #f0f0f0;
      border-radius: 18px;
      background: #ffffff;
    }

    .online-orders-input,
    .online-orders-select {
      min-height: 42px;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      color: #374151;
      font-size: 13px;
      font-weight: 500;
    }

    .online-orders-input:focus,
    .online-orders-select:focus {
      border-color: #e8650a;
      box-shadow: 0 0 0 0.2rem rgba(232, 101, 10, 0.12);
    }

    .online-orders-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 42px;
      border: 1px solid #e8650a;
      border-radius: 12px;
      background: #e8650a;
      color: #ffffff;
      font-size: 13px;
      font-weight: 700;
      text-decoration: none;
      transition: background 150ms ease, border-color 150ms ease;
    }

    .online-orders-btn:hover {
      border-color: #c2410c;
      background: #c2410c;
      color: #ffffff;
    }

    .online-orders-card {
      border: 1px solid #f0f0f0;
      border-radius: 18px;
      background: #ffffff;
      overflow: hidden;
    }

    .online-orders-card__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 18px 20px;
      border-bottom: 1px solid #f3f4f6;
    }

    .online-orders-card__title {
      margin: 0;
      color: #111827;
      font-size: 15px;
      font-weight: 700;
    }

    .online-orders-card__subtitle {
      margin-top: 4px;
      color: #9ca3af;
      font-size: 12px;
    }

    .online-orders-table {
      margin: 0;
    }

    .online-orders-table thead th {
      padding: 14px 20px;
      border-bottom: 1px solid #f3f4f6;
      color: #9ca3af;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      background: #fcfcfc;
      white-space: nowrap;
    }

    .online-orders-table tbody td {
      padding: 16px 20px;
      border-bottom: 1px solid #f3f4f6;
      color: #374151;
      font-size: 13px;
      vertical-align: middle;
    }

    .online-orders-code {
      color: #111827;
      font-size: 13px;
      font-weight: 700;
      white-space: nowrap;
    }

    .online-orders-buyer {
      color: #111827;
      font-weight: 600;
    }

    .online-orders-muted {
      color: #9ca3af;
      font-size: 12px;
      font-weight: 500;
    }

    .online-orders-price {
      color: #e8650a;
      font-weight: 700;
      white-space: nowrap;
    }

    .online-orders-status {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      padding: 0 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      text-transform: capitalize;
      white-space: nowrap;
    }

    .online-orders-status.is-pending { background: #fff7ed; color: #e8650a; }
    .online-orders-status.is-confirmed { background: #eff6ff; color: #2563eb; }
    .online-orders-status.is-processing { background: #f5f3ff; color: #7c3aed; }
    .online-orders-status.is-completed { background: #ecfdf5; color: #059669; }
    .online-orders-status.is-cancelled { background: #fef2f2; color: #dc2626; }

    .online-orders-detail {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 34px;
      padding: 0 12px;
      border: 1px solid #fed7aa;
      border-radius: 999px;
      background: #fff7ed;
      color: #e8650a;
      font-size: 12px;
      font-weight: 700;
      text-decoration: none;
      white-space: nowrap;
    }

    .online-orders-detail:hover {
      border-color: #fdba74;
      background: #ffedd5;
      color: #c2410c;
    }

    .online-orders-empty {
      padding: 72px 20px;
      color: #9ca3af;
      font-size: 13px;
      text-align: center;
    }

    .online-orders-pagination {
      padding: 16px 20px;
      border-top: 1px solid #f3f4f6;
    }

    @media (max-width: 991.98px) {
      .online-orders-page {
        padding: 20px 16px;
      }

      .online-orders-filter {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 767.98px) {
      .online-orders-page {
        padding: 16px 12px;
      }

      .online-orders-heading {
        flex-direction: column;
      }

      .online-orders-card__header {
        align-items: flex-start;
        flex-direction: column;
        padding: 16px;
      }

      .online-orders-table thead {
        display: none;
      }

      .online-orders-table tbody tr {
        display: grid;
        gap: 10px;
        padding: 16px;
        border-bottom: 1px solid #f3f4f6;
      }

      .online-orders-table tbody td {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 0;
        border: 0;
      }

      .online-orders-table tbody td::before {
        content: attr(data-label);
        flex: 0 0 88px;
        color: #9ca3af;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
      }
    }
  </style>
@endsection

@section('content')
<div class="online-orders-page">
  <div class="online-orders-heading">
    <div>
      <h1 class="online-orders-heading__title">Pesanan Online</h1>
      <div class="online-orders-heading__breadcrumb">CATcha POS / Pesanan Online</div>
    </div>
  </div>

  <form class="online-orders-filter" method="GET">
    <input class="form-control online-orders-input" name="q" value="{{ request('q') }}" placeholder="Cari kode order / buyer">
    <select class="form-select online-orders-select" name="status">
      <option value="">Semua Status</option>
      @foreach (['pending', 'confirmed', 'processing', 'completed', 'cancelled'] as $status)
        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
      @endforeach
    </select>
    <button class="online-orders-btn" type="submit">Filter</button>
  </form>

  <section class="online-orders-card">
    <div class="online-orders-card__header">
      <div>
        <h2 class="online-orders-card__title">Daftar Pesanan Online</h2>
        <div class="online-orders-card__subtitle">Kelola konfirmasi, proses, dan penyelesaian pickup online.</div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table online-orders-table align-middle">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Buyer</th>
            <th>Tanggal</th>
            <th>Item</th>
            <th>Total</th>
            <th>Bayar</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            <tr>
              <td data-label="Kode"><span class="online-orders-code">{{ $order->order_code }}</span></td>
              <td data-label="Buyer">
                <div class="online-orders-buyer">{{ $order->buyer?->name ?: '-' }}</div>
              </td>
              <td data-label="Tanggal"><span class="online-orders-muted">{{ $order->created_at->format('d/m/Y H:i') }}</span></td>
              <td data-label="Item"><span class="online-orders-muted">{{ $order->items_count }} item</span></td>
              <td data-label="Total"><span class="online-orders-price">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span></td>
              <td data-label="Bayar">
                <div>{{ $order->paymentMethodLabel() }}</div>
                <div class="online-orders-muted">{{ $order->payment_status }}</div>
              </td>
              <td data-label="Status"><span class="online-orders-status is-{{ $order->status }}">{{ $order->status }}</span></td>
              <td data-label="Aksi"><a class="online-orders-detail" href="{{ route('online-orders.show', $order) }}">Detail</a></td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="online-orders-empty">Belum ada pesanan online.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="online-orders-pagination">
      {{ $orders->links() }}
    </div>
  </section>
</div>
@endsection
