<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard_admin') ? '' : 'collapsed' }}" href="{{ route('dashboard_admin')}}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-heading">Master Data</li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.products.index', 'add_product', 'edit_product', 'delete_product') ? '' : 'collapsed' }}" href="{{ route('admin.products.index') }}">
          <i class="bx bx-cube"></i>
          <span>Produk</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.categories.index', 'add_category', 'edit_category', 'delete_category') ? '' : 'collapsed' }}" href="{{ route('admin.categories.index') }}">
          <i class="bx bx-category"></i>
          <span>Kategori</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('suppliers.*') ? '' : 'collapsed' }}" href="{{ route('suppliers.index') }}">
          <i class="bx bx-store-alt"></i>
          <span>Supplier</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('user_data', 'add_user', 'edit_user', 'delete_user') ? '' : 'collapsed' }}" href="{{ route('user_data')}}">
          <i class="bx bx-user"></i>
          <span>Kasir / User</span>
        </a>
      </li>

      <li class="nav-heading">Transaksi</li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('sales_data', 'detail_sales_data') ? '' : 'collapsed' }}" href="{{ route('sales_data')}}">
          <i class="bx bx-detail"></i>
          <span>Sales Data</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('purchases.*') ? '' : 'collapsed' }}" href="{{ route('purchases.index') }}">
          <i class="bx bx-package"></i>
          <span>Restock</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('stock_movement') ? '' : 'collapsed' }}" href="{{ route('stock_movement')}}">
          <i class="bx bx-detail"></i>
          <span>Stock Movement</span>
        </a>
      </li>

      <li class="nav-heading">Operasional</li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.shifts.*') ? '' : 'collapsed' }}" href="{{ route('admin.shifts.index') }}">
          <i class="bx bx-time"></i>
          <span>Shift Kasir</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.chatbot.logs') ? '' : 'collapsed' }}" href="{{ route('admin.chatbot.logs') }}">
          <i class="bx bx-bot"></i>
          <span>Audit Chatbot</span>
        </a>
      </li>

      <li class="nav-heading">Laporan</li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('reports.profit*') ? '' : 'collapsed' }}" href="{{ route('reports.profit') }}">
          <i class="bx bx-line-chart"></i>
          <span>Laporan Profit</span>
        </a>
      </li>

    </ul>

</aside><!-- End Sidebar-->
