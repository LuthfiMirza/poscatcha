@php
  $pageTitle = 'Dashboard Cashier';

  if (request()->routeIs('cashier.shift.open')) {
      $pageTitle = 'Buka Shift';
  } elseif (request()->routeIs('cashier.shift.close')) {
      $pageTitle = 'Tutup Shift';
  } elseif (request()->routeIs('list_product')) {
      $pageTitle = 'List Product';
  } elseif (request()->routeIs('selling_product')) {
      $pageTitle = 'Menu';
  } elseif (request()->routeIs('cashier_profile')) {
      $pageTitle = 'Profil Kasir';
  }

  $resolvedPageTitle = trim($__env->yieldContent('page-title', $pageTitle));
  $isPosPage = request()->routeIs('selling_product', 'list_product');
  $activeShift = $headerShiftInfo['shift'] ?? null;
  $shiftTransactionCount = $headerShiftInfo['transactions_count'] ?? 0;
@endphp

@if ($isPosPage)
  <header id="header" class="header fixed-top cashier-pos-header">
    <style>
      .cashier-pos-header {
        position: fixed;
        top: 0;
        left: 60px;
        right: 0;
        z-index: 1035;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 0 20px;
        background: #ffffff;
        border-bottom: 1px solid #f0f0f0;
        box-shadow: none;
      }

      .cashier-pos-header__left,
      .cashier-pos-header__right {
        display: flex;
        align-items: center;
        min-width: 0;
      }

      .cashier-pos-header__left {
        gap: 12px;
      }

      .cashier-pos-header__right {
        gap: 10px;
        margin-left: auto;
      }

      .cashier-pos-logo {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #eefbe6;
        border: 1px solid #d8eac8;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1;
      }

      .cashier-pos-logo__cat {
        color: #6aaa2a;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.02em;
      }

      .cashier-pos-logo__cha {
        color: #e8650a;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.02em;
      }

      .cashier-pos-header__title {
        color: #1a1a1a;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
      }

      .cashier-pos-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 32px;
        padding: 0 12px;
        border: 1px solid #f0f0f0;
        border-radius: 999px;
        background: #fafafa;
        color: #6f6f6f;
        font-size: 12px;
        font-weight: 500;
        line-height: 1;
        white-space: nowrap;
      }

      .cashier-pos-chip i {
        font-size: 13px;
      }

      .cashier-pos-chip--clock #clock-wib {
        color: #1a1a1a;
        font-variant-numeric: tabular-nums;
      }

      .cashier-pos-chip--clock span:last-child {
        color: #9e9e9e;
      }

      .cashier-pos-chip--transaction {
        background: #fef0e6;
        border-color: #f7c9a8;
        color: #b95413;
      }

      .cashier-pos-chip--transaction i {
        color: #e8650a;
      }

      .cashier-pos-chip--shift {
        background: #eefbe6;
        border-color: #cfe5bc;
        color: #4f8820;
      }

      .cashier-pos-chip__dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #6aaa2a;
        flex: 0 0 8px;
      }

      .cashier-pos-chip__meta {
        color: #6aaa2a;
        font-variant-numeric: tabular-nums;
      }

      .cashier-pos-close {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 0 16px;
        border: 1px solid #e8650a;
        border-radius: 10px;
        background: #e8650a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
      }

      .cashier-pos-close:hover,
      .cashier-pos-close:focus {
        background: #c85508;
        border-color: #c85508;
        color: #ffffff;
      }

      .cashier-pos-avatar {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        background: #1a1a1a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }

      @media (max-width: 1199.98px) {
        .cashier-pos-chip--transaction,
        .cashier-pos-chip--shift {
          display: none;
        }
      }

      @media (max-width: 991.98px) {
        .cashier-pos-header {
          padding: 0 12px;
        }

        .cashier-pos-header__title {
          font-size: 14px;
        }

        .cashier-pos-chip--clock {
          display: none;
        }
      }

      @media (max-width: 767.98px) {
        .cashier-pos-header {
          gap: 8px;
        }

        .cashier-pos-close span {
          display: none;
        }
      }
    </style>

    <div class="cashier-pos-header__left">
      <div class="cashier-pos-logo" aria-hidden="true">
        <span class="cashier-pos-logo__cat">CAT</span>
        <span class="cashier-pos-logo__cha">cha</span>
      </div>

      <div class="cashier-pos-header__title">{{ $resolvedPageTitle }}</div>
    </div>

    <div class="cashier-pos-header__right">
      <div class="cashier-pos-chip cashier-pos-chip--clock">
        <i class="bi bi-clock"></i>
        <span id="clock-wib">00:00:00</span>
        <span>WIB</span>
      </div>

      <div class="cashier-pos-chip cashier-pos-chip--transaction">
        <i class="bi bi-cart3"></i>
        <span>{{ $shiftTransactionCount }} transaksi</span>
      </div>

      <div class="cashier-pos-chip cashier-pos-chip--shift">
        <span class="cashier-pos-chip__dot"></span>
        <span>Shift aktif</span>
        <span id="shift-duration" class="cashier-pos-chip__meta">· 0j 0m</span>
      </div>

      @if ($activeShift)
        <a href="{{ route('cashier.shift.close') }}" class="cashier-pos-close">
          <i class="bi bi-power"></i>
          <span>Tutup kasir</span>
        </a>
      @endif

      <div class="cashier-pos-avatar" title="{{ Auth::user()->name }}">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
      </div>
    </div>
  </header>

  <script>
  (function(){
    const shiftStartStr = "{{ $activeShift?->shift_start?->toIso8601String() ?? now()->toIso8601String() }}";
    const shiftStart = new Date(shiftStartStr);
    function pad(n){ return String(n).padStart(2,'0'); }
    function tick(){
      const now = new Date();
      const wib = new Date(now.toLocaleString('en-US',{timeZone:'Asia/Jakarta'}));
      const el = document.getElementById('clock-wib');
      if(el) el.textContent = pad(wib.getHours())+':'+pad(wib.getMinutes())+':'+pad(wib.getSeconds());
      const dur = document.getElementById('shift-duration');
      if(dur){
        const diff = Math.floor((wib - shiftStart)/1000);
        const h = Math.floor(Math.abs(diff)/3600);
        const m = Math.floor((Math.abs(diff)%3600)/60);
        dur.textContent = '· '+h+'j '+m+'m';
      }
    }
    tick(); setInterval(tick,1000);
  })();
  </script>
@else
  <header id="header" class="header fixed-top d-flex align-items-center cashier-header">
    <style>
      .cashier-header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: none;
        padding: 0 20px;
        gap: 16px;
      }

      .cashier-header-left,
      .cashier-header-center,
      .cashier-header-right {
        display: flex;
        align-items: center;
        min-width: 0;
      }

      .cashier-header-left {
        flex: 0 0 auto;
        gap: 12px;
      }

      .cashier-header-center {
        flex: 1 1 auto;
        justify-content: center;
        min-width: 0;
      }

      .cashier-header-right {
        flex: 0 0 auto;
        margin-left: auto;
        gap: 12px;
      }

      .cashier-header .toggle-sidebar-btn {
        color: #0f172a;
        font-size: 1.35rem;
      }

      .cashier-page-meta {
        text-align: center;
        min-width: 0;
      }

      .cashier-page-caption {
        display: block;
        font-size: 11px;
        line-height: 1.2;
        color: #94a3b8;
        letter-spacing: 0.08em;
        text-transform: uppercase;
      }

      .cashier-page-title {
        display: block;
        font-size: 14px;
        line-height: 1.3;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .cashier-shift-wrap {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
      }

      .cashier-shift-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 12px;
        border-radius: 999px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
      }

      .cashier-shift-pill.is-inactive {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
      }

      .cashier-shift-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
        flex: 0 0 8px;
      }

      .cashier-shift-detail {
        font-size: 11px;
        color: #64748b;
        white-space: nowrap;
      }

      .cashier-shift-close {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.2;
      }

      .cashier-shift-close:hover,
      .cashier-shift-close:focus {
        background: #b91c1c;
        border-color: #b91c1c;
        color: #fff;
      }

      .cashier-profile-trigger {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 4px 6px 4px 4px;
        border-radius: 999px;
        background: #fff;
        color: #0f172a;
        text-decoration: none;
      }

      .cashier-profile-trigger:hover,
      .cashier-profile-trigger:focus {
        background: #f8fafc;
        color: #0f172a;
      }

      .cashier-profile-avatar {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        overflow: hidden;
        flex: 0 0 34px;
        border: 1px solid #e2e8f0;
        background: #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }

      .cashier-profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      .cashier-profile-name {
        font-size: 13px;
        font-weight: 500;
        color: #0f172a;
        white-space: nowrap;
      }

      .cashier-chevron {
        color: #64748b;
        flex: 0 0 auto;
      }

      @media (max-width: 991.98px) {
        .cashier-header {
          padding: 0 12px;
        }

        .cashier-header-center {
          justify-content: flex-start;
        }

        .cashier-page-caption {
          display: none;
        }
      }

      @media (max-width: 767.98px) {
        .cashier-header-right {
          gap: 8px;
        }

        .cashier-shift-detail {
          display: none;
        }

        .cashier-profile-name {
          display: none;
        }

        .cashier-page-title {
          font-size: 13px;
        }
      }
    </style>

    <div class="cashier-header-left">
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <div class="cashier-header-center">
      <div class="cashier-page-meta">
        <span class="cashier-page-caption">Cashier Panel</span>
        <span class="cashier-page-title">{{ $resolvedPageTitle }}</span>
      </div>
    </div>

    <div class="cashier-header-right">
      <div class="cashier-shift-wrap">
        @if (!empty($headerShiftInfo))
          <div class="cashier-shift-pill">
            <span class="cashier-shift-dot"></span>
            <span>Shift Aktif</span>
          </div>
          <div class="cashier-shift-detail">
            Mulai: {{ $headerShiftInfo['shift']->shift_start->format('d M Y H:i') }} &middot; {{ $headerShiftInfo['transactions_count'] }} transaksi
          </div>
        @else
          <div class="cashier-shift-pill is-inactive">
            <span class="cashier-shift-dot"></span>
            <span>Tidak Ada Shift</span>
          </div>
        @endif
      </div>

      @if (!empty($headerShiftInfo))
        <a href="{{ route('cashier.shift.close') }}" class="btn cashier-shift-close">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3V12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M7.05 5.8A8 8 0 1 0 16.95 5.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>Tutup Shift</span>
        </a>
      @endif

      <li class="nav-item dropdown pe-0 list-unstyled">
        <a class="nav-link nav-profile cashier-profile-trigger" href="#" data-bs-toggle="dropdown">
          <span class="cashier-profile-avatar">
            <img src="{{ asset('storage/assets/photo/profile.jpeg')}}" alt="Profile">
          </span>
          <span class="cashier-profile-name">{{ Auth::user()->name }}</span>
          <span class="cashier-chevron" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
              <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6>{{ Auth::user()->name }}</h6>
            <span>Cashier</span>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('cashier_profile') }}">
              <i class="bi bi-person"></i>
              <span>Profil</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <a class="dropdown-item d-flex align-items-center" href="" onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
              </a>
            </form>
          </li>
        </ul>
      </li>
    </div>
  </header>
@endif
