@extends('layouts.cashier')

@section('page-title', 'Detail Pesanan Online')

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

    .online-order-page {
      min-height: calc(100vh - 56px);
      padding: 24px 32px;
      background: #ffffff;
    }

    .online-order-heading {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 24px;
    }

    .online-order-heading__title {
      margin: 0;
      color: #111827;
      font-size: 20px;
      font-weight: 600;
      line-height: 1.2;
    }

    .online-order-heading__breadcrumb {
      margin-top: 4px;
      color: #9ca3af;
      font-size: 12px;
      line-height: 1.4;
    }

    .online-order-heading__breadcrumb a {
      color: #6b7280;
      text-decoration: none;
    }

    .online-order-heading__breadcrumb a:hover {
      color: #e8650a;
    }

    .online-order-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      min-height: 40px;
      padding: 0 14px;
      border: 1px solid #fed7aa;
      border-radius: 999px;
      background: #fff7ed;
      color: #e8650a;
      font-size: 12px;
      font-weight: 600;
      text-decoration: none;
      transition: background 150ms ease, border-color 150ms ease;
    }

    .online-order-back:hover {
      border-color: #fdba74;
      background: #ffedd5;
      color: #c2410c;
    }

    .online-order-layout {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 360px;
      gap: 20px;
      align-items: start;
    }

    .online-order-card {
      border: 1px solid #f0f0f0;
      border-radius: 18px;
      background: #ffffff;
      overflow: hidden;
    }

    .online-order-card__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 18px 20px;
      border-bottom: 1px solid #f3f4f6;
    }

    .online-order-card__title {
      margin: 0;
      color: #111827;
      font-size: 15px;
      font-weight: 700;
    }

    .online-order-card__subtitle {
      margin-top: 4px;
      color: #9ca3af;
      font-size: 12px;
    }

    .online-order-total-chip {
      display: inline-flex;
      align-items: center;
      min-height: 34px;
      padding: 0 12px;
      border-radius: 999px;
      background: #fff7ed;
      color: #e8650a;
      font-size: 13px;
      font-weight: 700;
      white-space: nowrap;
    }

    .online-order-table {
      margin: 0;
    }

    .online-order-table thead th {
      padding: 14px 20px;
      border-bottom: 1px solid #f3f4f6;
      color: #9ca3af;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      background: #fcfcfc;
    }

    .online-order-table tbody td {
      padding: 16px 20px;
      border-bottom: 1px solid #f3f4f6;
      color: #374151;
      font-size: 13px;
      vertical-align: middle;
    }

    .online-order-table tfoot th {
      padding: 16px 20px;
      border: 0;
      color: #111827;
      font-size: 14px;
      font-weight: 700;
      background: #fffaf5;
    }

    .online-order-product {
      color: #111827;
      font-size: 14px;
      font-weight: 600;
      line-height: 1.35;
    }

    .online-order-product small {
      display: block;
      margin-top: 4px;
      color: #9ca3af;
      font-size: 11px;
      font-weight: 500;
    }

    .online-order-qty {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      height: 28px;
      border-radius: 999px;
      background: #f3f4f6;
      color: #374151;
      font-size: 12px;
      font-weight: 700;
    }

    .online-order-price {
      color: #111827;
      font-weight: 600;
      white-space: nowrap;
    }

    .online-order-side {
      display: grid;
      gap: 16px;
    }

    .online-order-info {
      padding: 18px 20px;
    }

    .online-order-status {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      padding: 0 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      text-transform: capitalize;
    }

    .online-order-status.is-pending { background: #fff7ed; color: #e8650a; }
    .online-order-status.is-confirmed { background: #eff6ff; color: #2563eb; }
    .online-order-status.is-processing { background: #f5f3ff; color: #7c3aed; }
    .online-order-status.is-completed { background: #ecfdf5; color: #059669; }
    .online-order-status.is-cancelled { background: #fef2f2; color: #dc2626; }

    .online-order-info-list {
      display: grid;
      gap: 14px;
      margin: 0;
    }

    .online-order-info-row {
      display: grid;
      gap: 4px;
    }

    .online-order-info-row dt {
      color: #9ca3af;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .online-order-info-row dd {
      margin: 0;
      color: #111827;
      font-size: 13px;
      font-weight: 600;
      line-height: 1.5;
    }

    .online-order-info-row small {
      color: #9ca3af;
      font-size: 12px;
      font-weight: 500;
    }

    .online-order-actions {
      display: grid;
      gap: 10px;
      padding: 18px 20px;
      border-top: 1px solid #f3f4f6;
      background: #fcfcfc;
    }

    .online-order-history {
      display: grid;
      gap: 12px;
      padding: 18px 20px;
    }

    .online-order-history__item {
      display: grid;
      gap: 4px;
      padding-left: 12px;
      border-left: 3px solid #fed7aa;
    }

    .online-order-history__title {
      color: #111827;
      font-size: 13px;
      font-weight: 800;
    }

    .online-order-history__meta,
    .online-order-history__note {
      color: #6b7280;
      font-size: 12px;
      line-height: 1.5;
    }

    .online-order-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 42px;
      width: 100%;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 700;
      text-decoration: none;
      transition: background 150ms ease, border-color 150ms ease, color 150ms ease;
    }

    .online-order-btn--primary {
      border: 1px solid #e8650a;
      background: #e8650a;
      color: #ffffff;
    }

    .online-order-btn--primary:hover {
      border-color: #c2410c;
      background: #c2410c;
      color: #ffffff;
    }

    .online-order-btn--success {
      border: 1px solid #059669;
      background: #059669;
      color: #ffffff;
    }

    .online-order-btn--success:hover {
      border-color: #047857;
      background: #047857;
      color: #ffffff;
    }

    .online-order-btn--outline {
      border: 1px solid #fed7aa;
      background: #ffffff;
      color: #e8650a;
    }

    .online-order-btn--outline:hover {
      border-color: #fdba74;
      background: #fff7ed;
      color: #c2410c;
    }

    .online-order-btn--danger {
      border: 1px solid #fecaca;
      background: #fff1f2;
      color: #dc2626;
    }

    .online-order-btn--danger:hover {
      border-color: #fca5a5;
      background: #fee2e2;
      color: #b91c1c;
    }

    .online-order-cancel-note {
      min-height: 92px;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      color: #374151;
      font-size: 13px;
      resize: vertical;
    }

    .online-order-cancel-note:focus {
      border-color: #e8650a;
      box-shadow: 0 0 0 0.2rem rgba(232, 101, 10, 0.12);
    }

    .online-order-confirm-modal .modal-content {
      border: 0;
      border-radius: 22px;
      box-shadow: 0 24px 70px rgba(15, 23, 42, 0.2);
      overflow: hidden;
    }

    .online-order-confirm-modal__body {
      padding: 28px;
      text-align: center;
    }

    .online-order-confirm-modal__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 56px;
      height: 56px;
      margin-bottom: 16px;
      border-radius: 999px;
      background: #fff7ed;
      color: #e8650a;
      font-size: 26px;
    }

    .online-order-confirm-modal__title {
      margin: 0;
      color: #111827;
      font-size: 18px;
      font-weight: 700;
    }

    .online-order-confirm-modal__text {
      margin: 10px auto 0;
      max-width: 340px;
      color: #6b7280;
      font-size: 13px;
      line-height: 1.6;
    }

    .online-order-confirm-modal__actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-top: 22px;
    }

    .online-order-confirm-modal__btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 42px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 700;
    }

    .online-order-confirm-modal__btn--cancel {
      border: 1px solid #e5e7eb;
      background: #ffffff;
      color: #6b7280;
    }

    .online-order-confirm-modal__btn--confirm {
      border: 1px solid #e8650a;
      background: #e8650a;
      color: #ffffff;
    }

    .online-order-confirm-modal__btn--confirm.is-danger {
      border-color: #dc2626;
      background: #dc2626;
    }

    @media (max-width: 1199.98px) {
      .online-order-layout {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 991.98px) {
      .online-order-page {
        padding: 20px 16px;
      }
    }

    @media (max-width: 767.98px) {
      .online-order-page {
        padding: 16px 12px;
      }

      .online-order-heading {
        flex-direction: column;
      }

      .online-order-back {
        width: 100%;
        justify-content: center;
      }

      .online-order-card__header {
        align-items: flex-start;
        flex-direction: column;
        padding: 16px;
      }

      .online-order-table thead {
        display: none;
      }

      .online-order-table tbody tr {
        display: grid;
        gap: 8px;
        padding: 16px;
        border-bottom: 1px solid #f3f4f6;
      }

      .online-order-table tbody td {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 0;
        border: 0;
      }

      .online-order-table tbody td::before {
        content: attr(data-label);
        color: #9ca3af;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
      }

      .online-order-table tfoot tr {
        display: flex;
        justify-content: space-between;
      }

      .online-order-table tfoot th {
        padding: 16px;
      }
    }
  </style>
@endsection

@section('content')
@php
  $statusClass = 'is-'.$order->status;
  $mustVerifyQris = $order->payment_method === \App\Models\Order::PAYMENT_QRIS
    && $order->payment_status !== \App\Models\Order::PAYMENT_STATUS_PAID
    && ! in_array($order->status, [\App\Models\Order::STATUS_COMPLETED, \App\Models\Order::STATUS_CANCELLED], true);
  $historyActionLabels = [
    'payment_verified' => 'Pembayaran Diverifikasi',
    'payment_rejected' => 'Pembayaran Ditolak',
    'confirmed' => 'Pesanan Diterima',
    'processing' => 'Pesanan Diproses',
    'completed' => 'Pesanan Selesai',
    'cancelled' => 'Pesanan Dibatalkan',
  ];
@endphp

<div class="online-order-page">
  <div class="online-order-heading">
    <div>
      <h1 class="online-order-heading__title">Detail Pesanan Online</h1>
      <div class="online-order-heading__breadcrumb">
        CATcha POS / <a href="{{ route('online-orders.index') }}">Pesanan Online</a> / {{ $order->order_code }}
      </div>
    </div>
    <a class="online-order-back" href="{{ route('online-orders.index') }}">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali ke Daftar</span>
    </a>
  </div>

  <div class="online-order-layout">
    <section class="online-order-card">
      <div class="online-order-card__header">
        <div>
          <h2 class="online-order-card__title">Item Pesanan</h2>
          <div class="online-order-card__subtitle">{{ $order->items->count() }} item untuk kode {{ $order->order_code }}</div>
        </div>
        <span class="online-order-total-chip">Total Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
      </div>

      <div class="table-responsive">
        <table class="table online-order-table align-middle">
          <thead>
            <tr>
              <th>Produk</th>
              <th class="text-center">Qty</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order->items as $item)
              <tr>
                <td data-label="Produk">
                  <div class="online-order-product">
                    {{ $item->product_name }}
                    <small>{{ $item->product_id }}</small>
                    @if ($item->customizationSummary())
                      <small>{{ $item->customizationSummary() }}</small>
                    @endif
                  </div>
                </td>
                <td data-label="Qty" class="text-center"><span class="online-order-qty">{{ $item->quantity }}</span></td>
                <td data-label="Harga"><span class="online-order-price">Rp{{ number_format($item->price, 0, ',', '.') }}</span></td>
                <td data-label="Subtotal"><span class="online-order-price">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span></td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3">Total Pembayaran</th>
              <th>Rp{{ number_format($order->total_price, 0, ',', '.') }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </section>

    <aside class="online-order-side">
      <section class="online-order-card">
        <div class="online-order-card__header">
          <div>
            <h2 class="online-order-card__title">Informasi Pesanan</h2>
            <div class="online-order-card__subtitle">Data buyer dan status workflow</div>
          </div>
          <span class="online-order-status {{ $statusClass }}">{{ $order->statusLabel() }}</span>
        </div>

        <div class="online-order-info">
          <dl class="online-order-info-list">
            <div class="online-order-info-row">
              <dt>Kode Order</dt>
              <dd>{{ $order->order_code }}</dd>
            </div>
            <div class="online-order-info-row">
              <dt>Buyer</dt>
              <dd>{{ $order->buyer?->name ?: '-' }}<br><small>{{ $order->buyer?->email ?: '-' }}</small></dd>
            </div>
            <div class="online-order-info-row">
              <dt>Pembayaran</dt>
              <dd>{{ $order->paymentMethodLabel() }} <small>/ {{ $order->paymentStatusLabel() }}</small></dd>
            </div>
            <div class="online-order-info-row">
              <dt>Catatan</dt>
              <dd>{{ $order->note ?: '-' }}</dd>
            </div>
            <div class="online-order-info-row">
              <dt>Dikonfirmasi Oleh</dt>
              <dd>{{ $order->confirmer?->name ?: '-' }}</dd>
            </div>
            <div class="online-order-info-row">
              <dt>Diselesaikan Oleh</dt>
              <dd>{{ $order->completer?->name ?: '-' }}</dd>
            </div>
            @if ($order->cancel_reason)
              <div class="online-order-info-row">
                <dt>Alasan Batal</dt>
                <dd>{{ $order->cancel_reason }}</dd>
              </div>
            @endif
          </dl>
        </div>

        <div class="online-order-actions">
          @if ($mustVerifyQris)
            <form id="verify-payment-form" method="POST" action="{{ route('online-orders.verify-payment', $order) }}">
              @csrf
              <button class="online-order-btn online-order-btn--primary" type="submit"><i class="bi bi-wallet2"></i> Verifikasi Pembayaran</button>
            </form>
            <form id="reject-payment-form" method="POST" action="{{ route('online-orders.reject-payment', $order) }}">
              @csrf
              <textarea class="form-control online-order-cancel-note mb-2" name="reason" placeholder="Alasan pembayaran ditolak (opsional)"></textarea>
              <button class="online-order-btn online-order-btn--danger" type="button" data-bs-toggle="modal" data-bs-target="#rejectPaymentModal"><i class="bi bi-wallet2"></i> Tolak Pembayaran</button>
            </form>
          @endif

          <a class="online-order-btn online-order-btn--outline" href="{{ route('online-orders.print', $order) }}" target="_blank"><i class="bi bi-printer"></i> Cetak Order</a>

          @if ($order->status === 'pending')
            <form id="confirm-order-form" method="POST" action="{{ route('online-orders.confirm', $order) }}">
              @csrf
              <button class="online-order-btn online-order-btn--success" type="button" data-bs-toggle="modal" data-bs-target="#confirmOrderModal" @disabled($mustVerifyQris)><i class="bi bi-check-circle"></i> Konfirmasi Pesanan</button>
            </form>
            <form id="cancel-pending-form" method="POST" action="{{ route('online-orders.cancel', $order) }}">
              @csrf
              <button class="online-order-btn online-order-btn--danger" type="button" data-bs-toggle="modal" data-bs-target="#cancelPendingModal"><i class="bi bi-x-circle"></i> Batalkan</button>
            </form>
          @elseif ($order->status === 'confirmed')
            <form method="POST" action="{{ route('online-orders.process', $order) }}">
              @csrf
              <button class="online-order-btn online-order-btn--primary" type="submit"><i class="bi bi-play-circle"></i> Mulai Proses</button>
            </form>
            <form id="cancel-confirmed-form" method="POST" action="{{ route('online-orders.cancel', $order) }}">
              @csrf
              <button class="online-order-btn online-order-btn--danger" type="button" data-bs-toggle="modal" data-bs-target="#cancelConfirmedModal"><i class="bi bi-x-circle"></i> Batalkan</button>
            </form>
          @elseif ($order->status === 'processing')
            <form id="complete-order-form" method="POST" action="{{ route('online-orders.complete', $order) }}">
              @csrf
              <button class="online-order-btn online-order-btn--success" type="button" data-bs-toggle="modal" data-bs-target="#completeOrderModal"><i class="bi bi-bag-check"></i> Selesaikan</button>
            </form>
            <form id="cancel-processing-form" method="POST" action="{{ route('online-orders.cancel', $order) }}">
              @csrf
              <textarea class="form-control online-order-cancel-note mb-2" name="cancel_reason" required placeholder="Alasan pembatalan"></textarea>
              <button class="online-order-btn online-order-btn--danger" type="button" data-bs-toggle="modal" data-bs-target="#cancelProcessingModal"><i class="bi bi-x-circle"></i> Batalkan</button>
            </form>
          @elseif ($order->status === 'completed' && $order->sale)
            <a class="online-order-btn online-order-btn--outline" href="{{ route('print.receipt', $order->sale->sale_id) }}"><i class="bi bi-printer"></i> Cetak Struk</a>
          @else
            <div class="text-center text-muted small">Tidak ada aksi tersedia untuk status ini.</div>
          @endif
        </div>
      </section>

      <section class="online-order-card">
        <div class="online-order-card__header">
          <div>
            <h2 class="online-order-card__title">Riwayat Status</h2>
            <div class="online-order-card__subtitle">Audit proses pembayaran dan pesanan</div>
          </div>
        </div>
        <div class="online-order-history">
          @forelse ($order->statusHistories as $history)
            <div class="online-order-history__item">
              <div class="online-order-history__title">{{ $historyActionLabels[$history->action] ?? ucfirst(str_replace('_', ' ', $history->action)) }}</div>
              <div class="online-order-history__meta">
                {{ $history->actor?->name ?: 'Sistem' }} • {{ $history->created_at->format('d M Y H:i') }}
              </div>
              <div class="online-order-history__note">
                Status: {{ $history->from_status ? (new \App\Models\Order(['status' => $history->from_status]))->statusLabel().' → ' : '' }}{{ (new \App\Models\Order(['status' => $history->to_status]))->statusLabel() }}
                @if ($history->from_payment_status || $history->to_payment_status)
                  <br>Pembayaran: {{ $history->from_payment_status ? (new \App\Models\Order(['payment_status' => $history->from_payment_status]))->paymentStatusLabel().' → ' : '' }}{{ (new \App\Models\Order(['payment_status' => $history->to_payment_status]))->paymentStatusLabel() }}
                @endif
                @if ($history->note)
                  <br>{{ $history->note }}
                @endif
              </div>
            </div>
          @empty
            <div class="text-muted small">Belum ada riwayat status.</div>
          @endforelse
        </div>
      </section>
    </aside>
  </div>
</div>

@if ($order->status === 'pending')
  @if ($mustVerifyQris)
    <x-cashier.order-confirm-modal
      id="rejectPaymentModal"
      form="reject-payment-form"
      icon="bi-wallet2"
      title="Tolak pembayaran QRIS?"
      message="Status pembayaran akan menjadi ditolak. Pembeli masih bisa membuka detail order untuk membayar ulang QRIS."
      confirm-label="Ya, Tolak Pembayaran"
      danger
    />
  @endif

  <x-cashier.order-confirm-modal
    id="confirmOrderModal"
    form="confirm-order-form"
    icon="bi-check-circle"
    title="Konfirmasi pesanan?"
    message="Pesanan {{ $order->order_code }} akan dikonfirmasi dan stok bahan/produk akan dikurangi sesuai item pesanan."
    confirm-label="Ya, Konfirmasi"
  />

  <x-cashier.order-confirm-modal
    id="cancelPendingModal"
    form="cancel-pending-form"
    icon="bi-x-circle"
    title="Batalkan pesanan?"
    message="Pesanan pending akan dibatalkan. Tidak ada stok yang dikembalikan karena stok belum dikurangi."
    confirm-label="Ya, Batalkan"
    danger
  />
@elseif ($order->status === 'confirmed')
  <x-cashier.order-confirm-modal
    id="cancelConfirmedModal"
    form="cancel-confirmed-form"
    icon="bi-arrow-counterclockwise"
    title="Batalkan dan restock?"
    message="Pesanan confirmed akan dibatalkan dan stok yang sudah dikurangi akan dikembalikan."
    confirm-label="Ya, Batalkan"
    danger
  />
@elseif ($order->status === 'processing')
  <x-cashier.order-confirm-modal
    id="completeOrderModal"
    form="complete-order-form"
    icon="bi-bag-check"
    title="Selesaikan pesanan?"
    message="Pesanan akan diselesaikan dan data penjualan online akan dibuat. Pastikan pesanan sudah diberikan ke buyer."
    confirm-label="Ya, Selesaikan"
  />

  <x-cashier.order-confirm-modal
    id="cancelProcessingModal"
    form="cancel-processing-form"
    icon="bi-x-circle"
    title="Batalkan pesanan processing?"
    message="Pesanan processing akan dibatalkan. Pastikan alasan pembatalan sudah diisi sebelum melanjutkan."
    confirm-label="Ya, Batalkan"
    danger
  />
@endif
@endsection
