@extends('layouts.cashier')

@section('page-title', 'Menu')

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

    .cashier-pos-page {
      min-height: calc(100vh - 56px);
      background: #ffffff;
    }

    .cashier-pos-breadcrumb {
      display: flex;
      align-items: center;
      min-height: 33px;
      padding: 0 20px;
      border-bottom: 1px solid #f0f0f0;
      color: #9e9e9e;
      font-size: 12px;
      font-weight: 400;
      background: #ffffff;
    }

    .cashier-pos-breadcrumb strong {
      color: #1a1a1a;
      font-weight: 500;
    }

    @media (max-width: 767.98px) {
      #main.main {
        margin-left: 60px !important;
      }

      .cashier-pos-breadcrumb {
        padding: 0 12px;
      }
    }
  </style>
@endsection

@section('content')
  <div class="cashier-pos-page">
    <div class="cashier-pos-breadcrumb">
      <span>CATcha POS / <strong>@yield('page-title')</strong></span>
    </div>

    <livewire:selling-product />
  </div>
@endsection
