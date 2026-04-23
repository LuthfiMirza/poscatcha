@php
  $isEdit = isset($purchase);
  $formAction = $isEdit ? route('purchases.update', $purchase) : route('purchases.store');
  $buttonLabel = $isEdit ? 'Update Purchase' : 'Simpan Purchase';
  $cancelRoute = $isEdit ? route('purchases.show', $purchase) : route('purchases.index');

  $defaultItems = $isEdit
      ? $purchase->items->map(fn ($item) => [
          'product_id' => $item->product_id,
          'quantity' => $item->quantity,
          'buy_price' => $item->buy_price,
      ])->toArray()
      : [['product_id' => '', 'quantity' => 1, 'buy_price' => '']];

  $oldItems = old('items', $defaultItems);
@endphp

<form method="POST" action="{{ $formAction }}">
  @csrf
  @if ($isEdit)
    @method('PUT')
  @endif

  <div class="row">
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Informasi Purchase</h5>

          @if ($isEdit)
            <div class="mb-3">
              <label class="form-label">Nomor Purchase</label>
              <input type="text" class="form-control" value="{{ $purchase->purchase_number }}" readonly>
            </div>
          @endif

          <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier Terdaftar</label>
            <select class="form-select" id="supplier_id" name="supplier_id">
              <option value="">Pilih Supplier</option>
              @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $isEdit ? $purchase->supplier_id : null) == $supplier->id)>{{ $supplier->name }}</option>
              @endforeach
            </select>
            <small class="text-muted">Kosongkan jika ingin isi supplier manual.</small>
          </div>

          <div class="mb-3">
            <label for="supplier_name" class="form-label">Nama Supplier Manual</label>
            <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $isEdit ? $purchase->supplier_name : null) }}" placeholder="Isi jika supplier belum ada di master">
          </div>

          <div class="mb-3">
            <label for="purchase_date" class="form-label">Tanggal Purchase</label>
            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $isEdit ? $purchase->purchase_date->toDateString() : now()->toDateString()) }}" required>
          </div>

          <div class="mb-3">
            <label for="invoice_number" class="form-label">Nomor Invoice</label>
            <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $isEdit ? $purchase->invoice_number : null) }}">
          </div>

          <div class="mb-3">
            <label for="notes" class="form-label">Catatan</label>
            <textarea class="form-control" id="notes" name="notes" rows="5">{{ old('notes', $isEdit ? $purchase->notes : null) }}</textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Item Purchase</h5>
            <button type="button" class="btn btn-success btn-sm" id="add-item-row">
              <i class="bx bx-plus"></i> Tambah Item
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="purchase-items-table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th width="120">Qty</th>
                  <th width="180">Harga Beli</th>
                  <th width="90">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($oldItems as $index => $item)
                  <tr>
                    <td>
                      <select name="items[{{ $index }}][product_id]" class="form-select" required>
                        <option value="">Pilih Produk</option>
                        @foreach ($products as $product)
                          <option value="{{ $product->product_id }}" @selected(($item['product_id'] ?? '') == $product->product_id)>
                            {{ $product->product_name }} ({{ $product->product_id }})
                          </option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <input type="number" name="items[{{ $index }}][quantity]" class="form-control" min="1" value="{{ $item['quantity'] ?? 1 }}" required>
                    </td>
                    <td>
                      <input type="number" name="items[{{ $index }}][buy_price]" class="form-control" min="1" value="{{ $item['buy_price'] ?? '' }}" required>
                    </td>
                    <td>
                      <button type="button" class="btn btn-danger btn-sm remove-item-row">Hapus</button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <button type="submit" class="btn btn-primary">{{ $buttonLabel }}</button>
          <a href="{{ $cancelRoute }}" class="btn btn-secondary">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</form>

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#purchase-items-table tbody');
    const addButton = document.getElementById('add-item-row');
    let rowIndex = {{ count($oldItems) }};

    function buildProductOptions() {
      return `{!! collect($products)->map(function ($product) {
          return '<option value="' . e($product->product_id) . '">' . e($product->product_name) . ' (' . e($product->product_id) . ')</option>';
      })->implode('') !!}`;
    }

    addButton.addEventListener('click', function () {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>
          <select name="items[${rowIndex}][product_id]" class="form-select" required>
            <option value="">Pilih Produk</option>
            ${buildProductOptions()}
          </select>
        </td>
        <td>
          <input type="number" name="items[${rowIndex}][quantity]" class="form-control" min="1" value="1" required>
        </td>
        <td>
          <input type="number" name="items[${rowIndex}][buy_price]" class="form-control" min="1" required>
        </td>
        <td>
          <button type="button" class="btn btn-danger btn-sm remove-item-row">Hapus</button>
        </td>
      `;

      tableBody.appendChild(row);
      rowIndex += 1;
    });

    tableBody.addEventListener('click', function (event) {
      if (!event.target.classList.contains('remove-item-row')) {
        return;
      }

      if (tableBody.querySelectorAll('tr').length === 1) {
        return;
      }

      event.target.closest('tr').remove();
    });
  });
</script>
@endsection
