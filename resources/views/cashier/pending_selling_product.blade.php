@extends('layouts.cashier')

@section('page-title', 'Pending Sales')

@section('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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

    .cashier-pending-page {
      min-height: calc(100vh - 56px);
      padding: 24px 32px;
      background: #ffffff;
    }

    .cashier-pending-heading__title {
      color: #111827;
      font-size: 20px;
      font-weight: 600;
      line-height: 1.2;
      margin: 0;
    }

    .cashier-pending-heading__breadcrumb {
      margin-top: 6px;
      color: #9ca3af;
      font-size: 12px;
      line-height: 1.4;
    }

    .cashier-pending-card {
      margin-top: 24px;
      border: 1px solid #f0f0f0;
      border-radius: 16px;
      background: #ffffff;
      overflow: hidden;
    }

    .cashier-pending-card .card-body {
      padding: 0;
    }

    .cashier-pending-card .card-title {
      display: none;
    }

    .cashier-pending-card .datatable-wrapper {
      padding: 0;
    }

    .cashier-pending-card .datatable-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 14px 20px;
      border-bottom: 1px solid #f0f0f0;
    }

    .cashier-pending-card .datatable-dropdown label,
    .cashier-pending-card .datatable-search label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin: 0;
      font-size: 0;
    }

    .cashier-pending-card .datatable-dropdown label::before {
      content: "Tampilkan";
      color: #6b7280;
      font-size: 12px;
      font-weight: 400;
    }

    .cashier-pending-card .datatable-dropdown label::after {
      content: "data";
      color: #6b7280;
      font-size: 12px;
      font-weight: 400;
    }

    .cashier-pending-card .datatable-selector {
      min-width: 74px;
      height: 32px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      background: #ffffff;
      color: #374151;
      font-size: 12px;
      padding: 0 32px 0 12px;
      box-shadow: none;
    }

    .cashier-pending-card .datatable-search {
      margin-left: auto;
    }

    .cashier-pending-card .datatable-input {
      width: 220px;
      height: 36px;
      border: 0;
      border-radius: 10px;
      background: #f5f5f5;
      color: #374151;
      font-size: 12px;
      padding: 0 12px;
      box-shadow: none;
    }

    .cashier-pending-card .datatable-input::placeholder {
      color: #9ca3af;
    }

    .cashier-pending-card .datatable-container {
      border: 0;
      padding: 0;
    }

    .cashier-pending-table {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
    }

    .cashier-pending-table thead tr {
      background: #f8f8f8;
      border-bottom: 1px solid #f0f0f0;
    }

    .cashier-pending-table th {
      padding: 12px 20px;
      color: #6b7280;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      text-align: left;
      white-space: nowrap;
    }

    .cashier-pending-table .datatable-sorter {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: inherit;
      text-decoration: none;
      padding-right: 14px;
    }

    .cashier-pending-table .datatable-sorter::before,
    .cashier-pending-table .datatable-sorter::after {
      content: "";
      position: absolute;
      right: 0;
      border-left: 4px solid transparent;
      border-right: 4px solid transparent;
    }

    .cashier-pending-table .datatable-sorter::before {
      top: 5px;
      border-bottom: 5px solid #d1d5db;
    }

    .cashier-pending-table .datatable-sorter::after {
      top: 13px;
      border-top: 5px solid #d1d5db;
    }

    .cashier-pending-table th.asc .datatable-sorter::before {
      border-bottom-color: #6b7280;
    }

    .cashier-pending-table th.desc .datatable-sorter::after {
      border-top-color: #6b7280;
    }

    .cashier-pending-table tbody tr {
      border-bottom: 1px solid #f8f8f8;
      transition: background-color 160ms ease;
    }

    .cashier-pending-table tbody tr:hover {
      background: #fafafa;
    }

    .cashier-pending-table td,
    .cashier-pending-table th[scope="row"] {
      padding: 14px 20px;
      color: #374151;
      font-size: 13px;
      vertical-align: middle;
    }

    .cashier-pending-table tbody th[scope="row"] {
      color: #9ca3af;
      font-size: 12px;
      font-weight: 400;
    }

    .cashier-pending-table tbody th[scope="row"] a {
      color: inherit;
      text-decoration: none;
    }

    .cashier-pending-table td:nth-child(2) {
      color: #111827;
      font-weight: 500;
    }

    .cashier-pending-table td:nth-child(2) .cashier-pending-cart-badge {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 0 10px;
      border: 1px solid #fed7aa;
      border-radius: 999px;
      background: #fff7ed;
      color: #e8650a;
      font-size: 11px;
      font-weight: 500;
    }

    .cashier-pending-table td:nth-child(3) {
      color: #111827;
      font-weight: 600;
    }

    .cashier-pending-table td:nth-child(4) {
      color: #6b7280;
      font-size: 12px;
    }

    .cashier-pending-table td:last-child {
      white-space: nowrap;
    }

    .cashier-pending-table td:last-child .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 32px;
      padding: 0 12px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 500;
      text-decoration: none;
      margin-right: 6px;
      box-shadow: none;
    }

    .cashier-pending-table td:last-child .btn:last-child {
      margin-right: 0;
    }

    .cashier-pending-table td:last-child .bg-primary {
      border: 1px solid #e8650a;
      background: #e8650a !important;
      color: #ffffff !important;
    }

    .cashier-pending-table td:last-child .bg-primary:hover {
      border-color: #c85508;
      background: #c85508 !important;
      color: #ffffff !important;
    }

    .cashier-pending-table td:last-child .bg-danger {
      border: 1px solid #fecaca;
      background: #ffffff !important;
      color: #ef4444 !important;
    }

    .cashier-pending-table td:last-child .bg-danger:hover {
      background: #fef2f2 !important;
      color: #ef4444 !important;
    }

    .cashier-pending-card .datatable-empty {
      padding: 64px 20px !important;
      border-bottom: 0 !important;
      color: transparent !important;
      font-size: 0 !important;
      text-align: center;
    }

    .cashier-pending-card .datatable-empty::before {
      content: "🛒";
      display: block;
      margin-bottom: 12px;
      color: #e0e0e0;
      font-size: 40px;
      line-height: 1;
    }

    .cashier-pending-card .datatable-empty::after {
      content: "Belum ada pesanan pending";
      display: block;
      color: #9ca3af;
      font-size: 13px;
      font-weight: 400;
    }

    .cashier-pending-card .datatable-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 14px 20px;
      border-top: 1px solid #f0f0f0;
    }

    .cashier-pending-card .datatable-info {
      margin: 0;
      color: #9ca3af;
      font-size: 12px;
    }

    .cashier-pending-card .datatable-pagination ul {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .cashier-pending-card .datatable-pagination a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      background: #ffffff;
      color: #6b7280;
      font-size: 12px;
      text-decoration: none;
      box-shadow: none;
    }

    .cashier-pending-card .datatable-pagination .active a {
      border-color: #e8650a;
      background: #e8650a;
      color: #ffffff;
    }

    .cashier-pending-card .datatable-pagination .disabled a {
      opacity: 0.45;
      pointer-events: none;
    }

    @media (max-width: 991.98px) {
      .cashier-pending-page {
        padding: 20px 16px;
      }

      .cashier-pending-card .datatable-top,
      .cashier-pending-card .datatable-bottom {
        flex-direction: column;
        align-items: stretch;
      }

      .cashier-pending-card .datatable-search {
        width: 100%;
        margin-left: 0;
      }

      .cashier-pending-card .datatable-input {
        width: 100%;
      }
    }

    @media (max-width: 767.98px) {
      .cashier-pending-page {
        padding: 16px 12px;
      }
    }
  </style>
@endsection

@section('content')
<div class="cashier-pending-page">
  <div class="cashier-pending-heading">
    <h1 class="cashier-pending-heading__title">Pending sales</h1>
    <div class="cashier-pending-heading__breadcrumb">Home / Pending Selling Product</div>
  </div>

  <section class="cashier-pending-card">
    <div class="card recent-sales overflow-auto border-0 shadow-none mb-0">
      <div class="card-body">
        <h5 class="card-title">Pending Sales</h5>

        <table class="table table-borderless datatable cashier-pending-table">
          <thead>
            <tr>
              <th scope="col">No</th>
              <th scope="col">Cart ID</th>
              <th scope="col">Amount</th>
              <th scope="col">Date</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pendings as $index => $pending)
            <tr>
              <th scope="row"><a href="#">{{ $index + 1}}</a></th>
              <td><span class="cashier-pending-cart-badge">{{ $pending->cart_id}}</span></td>
              <td>Rp{{ number_format($pending->amount) }}</td>
              <td>{{ date('d F Y', strtotime($pending->created_at)) }}</td>
              <td>
                <a href="{{ route('detail_pending_selling_product', $pending->cart_id)}}" class="btn badge bg-primary">Detail</a>
                <a href="{{ route('delete_pending_selling_product', $pending->id)}}" class="btn badge bg-danger">Delete</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>
@endsection
