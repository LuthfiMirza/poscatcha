@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

@include('admin.partials.flash')

<section class="section dashboard">
  <div class="row">
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card sales-card">
        <div class="card-body">
          <h5 class="card-title">Total Produk</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-box-seam"></i>
            </div>
            <div class="ps-3">
              <h6>{{ number_format($stats['total_products']) }}</h6>
              <span class="text-muted small pt-2 ps-1">Produk aktif di katalog</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card revenue-card">
        <div class="card-body">
          <h5 class="card-title">Penjualan Hari Ini</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-receipt"></i>
            </div>
            <div class="ps-3">
              <h6>Rp{{ number_format($stats['sales_today_total'], 2, ',', '.') }}</h6>
              <span class="text-muted small pt-2 ps-1">{{ $stats['sales_today_count'] }} transaksi</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card customers-card">
        <div class="card-body">
          <h5 class="card-title">Stok Menipis</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="ps-3">
              <h6>{{ number_format($stats['low_stock_count']) }}</h6>
              <span class="text-muted small pt-2 ps-1">Produk stok 5 atau kurang</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card revenue-card">
        <div class="card-body">
          <h5 class="card-title">Expired 30 Hari</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-calendar2-week"></i>
            </div>
            <div class="ps-3">
              <h6>{{ number_format($stats['expiring_count']) }}</h6>
              <span class="text-muted small pt-2 ps-1">Perlu dipantau segera</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card sales-card">
        <div class="card-body">
          <h5 class="card-title">Supplier</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-shop"></i>
            </div>
            <div class="ps-3">
              <h6>{{ number_format($stats['supplier_count']) }}</h6>
              <span class="text-muted small pt-2 ps-1">Relasi pemasok tercatat</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card customers-card">
        <div class="card-body">
          <h5 class="card-title">Shift Aktif</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-clock-history"></i>
            </div>
            <div class="ps-3">
              <h6>{{ number_format($stats['active_shifts']) }}</h6>
              <span class="text-muted small pt-2 ps-1">Kasir sedang bertugas</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Quick Actions</h5>
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
              <i class="bi bi-box-seam me-1"></i> Lihat Produk
            </a>
            <a href="{{ route('add_product') }}" class="btn btn-success">
              <i class="bi bi-plus-circle me-1"></i> Tambah Produk
            </a>
            <a href="{{ route('purchases.create') }}" class="btn btn-outline-primary">
              <i class="bi bi-bag-plus me-1"></i> Restock Baru
            </a>
            <a href="{{ route('sales_data') }}" class="btn btn-outline-secondary">
              <i class="bi bi-receipt me-1"></i> Lihat Penjualan
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-success">
              <i class="bi bi-shop me-1"></i> Supplier
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Transaksi Terbaru</h5>
          <table class="table table-borderless mb-0">
            <thead>
              <tr>
                <th>Invoice</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($recentSales as $sale)
                <tr>
                  <td>
                    <a href="{{ route('detail_sales_data', $sale->sale_id) }}" class="fw-semibold">
                      {{ $sale->sale_id }}
                    </a>
                    <div class="small text-muted">{{ $sale->created_at->format('d M Y H:i') }}</div>
                  </td>
                  <td>{{ $cashiers[$sale->cashier_id]->name ?? 'Kasir' }}</td>
                  <td>
                    @if ($sale->payment_method == 1)
                      Cash
                    @elseif ($sale->payment_method == 2)
                      Transfer
                    @elseif ($sale->payment_method == 3)
                      QRIS
                    @else
                      -
                    @endif
                  </td>
                  <td>Rp{{ number_format($sale->total, 2, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">Belum ada transaksi terbaru.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Restock Terbaru</h5>
            <a href="{{ route('purchases.index') }}" class="small">Lihat semua</a>
          </div>
          <div class="list-group list-group-flush pt-3">
            @forelse ($recentPurchases as $purchase)
              <div class="list-group-item px-0">
                <div class="fw-semibold">{{ $purchase->purchase_number }}</div>
                <div class="small text-muted">{{ $purchase->supplier_label }}</div>
                <div class="small text-muted">{{ $purchase->purchase_date->format('d M Y') }}</div>
              </div>
            @empty
              <div class="text-muted small py-2">Belum ada data restock.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Low Stock</h5>
            <a href="{{ route('admin.products.index') }}" class="small">Lihat Produk</a>
          </div>
          <div class="table-responsive pt-3">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Stok</th>
                  <th>Harga Jual</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($lowStockProducts as $product)
                  <tr>
                    <td>{{ $product->product_name }}</td>
                    <td><span class="badge bg-warning text-dark">{{ $product->product_quantity }}</span></td>
                    <td>Rp{{ number_format($product->product_price, 2, ',', '.') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">Tidak ada produk low stock.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Akan Expired</h5>
            <a href="{{ route('admin.products.index') }}" class="small">Lihat Produk</a>
          </div>
          <div class="table-responsive pt-3">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Expired</th>
                  <th>Stok</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($expiringProducts as $product)
                  <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($product->product_expired)->format('d M Y') }}</td>
                    <td>{{ $product->product_quantity }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">Tidak ada produk yang akan expired dalam 30 hari.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Ringkasan Kategori</h5>
            <a href="{{ route('add_category') }}" class="btn btn-sm btn-success">
              <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
            </a>
          </div>
          <div class="table-responsive pt-3">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Kategori</th>
                  <th>Kode</th>
                  <th>Total Produk</th>
                  <th>Dibuat</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($categories as $category)
                  <tr>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->category_id }}</td>
                    <td>{{ $categoryProductCounts[$category->category_id] ?? 0 }}</td>
                    <td>{{ $category->created_at->format('d M Y') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
