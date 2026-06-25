@php
  $isPosPage = request()->routeIs('selling_product', 'list_product', 'online-orders.*');
  $isShiftNav = request()->routeIs('cashier.shift.open') || request()->is('cashier/shift/open');
  $isCloseNav = request()->routeIs('cashier.shift.close') || request()->is('cashier/shift/close');
  $isMenuNav = request()->routeIs('list_product') || request()->is('list_product*');
  $isCashierNav = request()->routeIs('selling_product') || request()->is('selling_product*');
  $isOnlineOrderNav = request()->routeIs('online-orders.*');
@endphp

@if ($isPosPage)
  <aside id="sidebar" class="sidebar cashier-pos-sidebar">
    <style>
      .cashier-pos-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 60px;
        height: 100vh;
        padding: 8px 0 14px;
        background: #ffffff;
        border-right: 1px solid #f0f0f0;
        box-shadow: none;
      }

      .cashier-pos-sidebar__inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
      }

      .cashier-pos-sidebar__nav {
        width: 100%;
        margin: 8px 0 0;
        padding: 0;
      }

      .cashier-pos-sidebar__nav .nav-item {
        width: 100%;
        margin: 0;
      }

      .cashier-pos-sidebar__link {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        width: 100%;
        min-height: 56px;
        padding: 6px 4px;
        color: #9e9e9e;
        text-decoration: none;
        transition: color 160ms ease;
      }

      .cashier-pos-sidebar__link:hover,
      .cashier-pos-sidebar__link:focus {
        color: #1a1a1a;
      }

      .cashier-pos-sidebar__link.is-active {
        color: #e8650a;
      }

      .cashier-pos-sidebar__link.is-active::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        width: 2px;
        height: 24px;
        border-radius: 999px;
        background: #e8650a;
        transform: translateY(-50%);
      }

      .cashier-pos-sidebar__icon {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
      }

      .cashier-pos-sidebar__label {
        font-size: 9px;
        line-height: 1;
        font-weight: 500;
        text-align: center;
      }

      .cashier-pos-sidebar__avatar {
        margin-top: auto;
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: #1a1a1a;
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
      }
    </style>

    <div class="cashier-pos-sidebar__inner">
      <ul class="sidebar-nav cashier-pos-sidebar__nav" id="sidebar-nav">
        <li class="nav-item">
          <a class="cashier-pos-sidebar__link {{ $isShiftNav ? 'is-active' : '' }}" href="{{ route('cashier.shift.open') }}">
            <svg class="cashier-pos-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"></circle>
              <path d="M12 7.5V12L15 13.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <span class="cashier-pos-sidebar__label">Shift</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="cashier-pos-sidebar__link {{ $isCloseNav ? 'is-active' : '' }}" href="{{ route('cashier.shift.close') }}">
            <svg class="cashier-pos-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 3.75V10.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
              <path d="M7.76 5.85A8.25 8.25 0 1 0 16.24 5.85" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
            </svg>
            <span class="cashier-pos-sidebar__label">Close</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="cashier-pos-sidebar__link {{ $isMenuNav ? 'is-active' : '' }}" href="{{ route('list_product') }}">
            <svg class="cashier-pos-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4.5 4.5H10V10H4.5V4.5Z" stroke="currentColor" stroke-width="1.8"></path>
              <path d="M14 4.5H19.5V10H14V4.5Z" stroke="currentColor" stroke-width="1.8"></path>
              <path d="M4.5 14H10V19.5H4.5V14Z" stroke="currentColor" stroke-width="1.8"></path>
              <path d="M14 14H19.5V19.5H14V14Z" stroke="currentColor" stroke-width="1.8"></path>
            </svg>
            <span class="cashier-pos-sidebar__label">Menu</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="cashier-pos-sidebar__link {{ $isCashierNav ? 'is-active' : '' }}" href="{{ route('selling_product') }}">
            <svg class="cashier-pos-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="9" cy="18.5" r="1.5" fill="currentColor"></circle>
              <circle cx="17" cy="18.5" r="1.5" fill="currentColor"></circle>
              <path d="M3.5 5H5.5L7.6 14.25H18.3L20 8.25H8.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <span class="cashier-pos-sidebar__label">Cashier</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="cashier-pos-sidebar__link {{ $isOnlineOrderNav ? 'is-active' : '' }}" href="{{ route('online-orders.index') }}">
            <svg class="cashier-pos-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M5 6.5H19V18.5H5V6.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
              <path d="M8 10H16M8 14H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
            </svg>
            <span class="cashier-pos-sidebar__label">Online</span>
          </a>
        </li>
      </ul>

      <a href="{{ route('cashier_profile') }}" class="cashier-pos-sidebar__avatar" title="{{ Auth::user()->name }}">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
      </a>
    </div>
  </aside>
@else
  <aside id="sidebar" class="sidebar cashier-sidebar">
    <style>
      .cashier-sidebar {
        background: #1e293b;
        border-right: 1px solid #334155;
        padding: 0;
        color: #cbd5e1;
      }

      .cashier-sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 16px;
        border-bottom: 1px solid #334155;
        text-decoration: none;
      }

      .cashier-sidebar-brand img {
        width: 38px;
        height: 38px;
        object-fit: cover;
        border-radius: 10px;
        flex: 0 0 38px;
      }

      .cashier-sidebar-brand-name {
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.2;
      }

      .cashier-sidebar-nav {
        padding: 12px 0 16px;
      }

      .cashier-sidebar-section {
        padding: 0 16px;
        margin: 16px 0 8px;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
      }

      .cashier-sidebar .nav-item {
        margin: 0;
      }

      .cashier-sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 8px;
        padding: 10px 16px;
        border-radius: 8px;
        color: #cbd5e1;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 160ms ease, color 160ms ease;
      }

      .cashier-sidebar-link:hover,
      .cashier-sidebar-link:focus {
        background: #334155;
        color: #fff;
      }

      .cashier-sidebar-link.is-active {
        background: #3b82f6;
        color: #fff;
      }

      .cashier-sidebar-link svg {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        flex-shrink: 0;
        vertical-align: middle;
      }

      .cashier-sidebar-link span {
        min-width: 0;
        line-height: 1.25;
      }
    </style>

    <a href="{{ route('selling_product') }}" class="cashier-sidebar-brand">
      <img src="{{ asset('assets/img/logocat.jpeg') }}" alt="Logo">
      <span class="cashier-sidebar-brand-name">Dashboard Cashier</span>
    </a>

    <ul class="sidebar-nav cashier-sidebar-nav" id="sidebar-nav">
      <li class="cashier-sidebar-section">Operasional</li>

      <li class="nav-item">
        <a class="cashier-sidebar-link {{ request()->routeIs('cashier.shift.open') ? 'is-active' : '' }}" href="{{ route('cashier.shift.open') }}">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"></circle>
            <path d="M10 8.75V15.25L15.25 12L10 8.75Z" fill="currentColor"></path>
          </svg>
          <span>Buka Shift</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="cashier-sidebar-link {{ request()->routeIs('cashier.shift.close') ? 'is-active' : '' }}" href="{{ route('cashier.shift.close') }}">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3.75V10.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
            <path d="M7.58 5.94A8.25 8.25 0 1 0 16.42 5.94" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
          </svg>
          <span>Tutup Shift</span>
        </a>
      </li>

      <li class="cashier-sidebar-section">Transaksi</li>

      <li class="nav-item">
        <a class="cashier-sidebar-link {{ request()->routeIs('list_product') ? 'is-active' : '' }}" href="{{ route('list_product')}}">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4.5 7.5 12 4.25 19.5 7.5 12 10.75 4.5 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
            <path d="M4.5 7.5V16.5L12 19.75L19.5 16.5V7.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
            <path d="M12 10.75V19.75" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
          </svg>
          <span>List Product</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="cashier-sidebar-link {{ request()->routeIs('selling_product') ? 'is-active' : '' }}" href="{{ route('selling_product')}}">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="9" cy="19" r="1.5" fill="currentColor"></circle>
            <circle cx="17" cy="19" r="1.5" fill="currentColor"></circle>
            <path d="M3 4H5L7.2 14.25H18.2L20.35 8H8.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
          </svg>
          <span>Selling Product</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="cashier-sidebar-link {{ request()->routeIs('online-orders.*') ? 'is-active' : '' }}" href="{{ route('online-orders.index') }}">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 6.5H19V18.5H5V6.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
            <path d="M8 10H16M8 14H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
          </svg>
          <span>Pesanan Online</span>
        </a>
      </li>
    </ul>
  </aside>
@endif
