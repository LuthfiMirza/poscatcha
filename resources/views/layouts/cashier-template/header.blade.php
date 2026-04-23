<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ route('list_product')}}" class="logo d-flex align-items-center">
      <img src="{{ asset('assets/img/logocat.jpeg')}}" alt="" class="img-fluid" style="max-height: 65px;">
        <span class="d-none d-lg-block">Dashboard Cashier</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item d-none d-lg-block me-3">
          @if (!empty($headerShiftInfo))
            <div class="small text-end">
              <div><strong>{{ Auth::user()->name }}</strong> | Shift aktif</div>
              <div>Mulai: {{ $headerShiftInfo['shift']->shift_start->format('d M Y H:i') }}</div>
              <div>Total transaksi: {{ $headerShiftInfo['transactions_count'] }}</div>
            </div>
          @else
            <div class="small text-end text-danger">
              Shift belum dibuka
            </div>
          @endif
        </li>

        <li class="nav-item me-3">
          @if (!empty($headerShiftInfo))
            <a href="{{ route('cashier.shift.close') }}" class="btn btn-outline-danger btn-sm">Tutup Shift</a>
          @else
            <a href="{{ route('cashier.shift.open') }}" class="btn btn-outline-primary btn-sm">Buka Shift</a>
          @endif
        </li>

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="{{ asset('storage/assets/photo/profile.jpeg')}}" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span>
          </a><!-- End Profile Iamge Icon -->

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
                <span>My Profile</span>
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
                  <span>Sign Out</span>
                </a>
              </form>

            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->
