<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('cashier.shift.open') }}">
          <i class="bx bx-time-five"></i>
          <span>Buka Shift</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('cashier.shift.close') }}">
          <i class="bx bx-stop-circle"></i>
          <span>Tutup Shift</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('list_product')}}">
          <i class="bx bxs-food-menu"></i>
          <span>List Product</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('pending_selling_product')}}">
          <i class="bx bxs-archive-in"></i>
          <span>Pending Selling Product</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('selling_product')}}">
          <i class="bx bxs-calculator"></i>
          <span>Selling Product</span>
        </a>
      </li><!-- End Dashboard Nav -->
    </ul>

</aside><!-- End Sidebar-->
