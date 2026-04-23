<div class="row">
    <!-- Product Card -->
    <div class="col-lg-6">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Product</h5>
          <table class="table table-hover">
            <thead> 
              <tr>
                <th scope="col">Product ID</th>
                <th scope="col">Product Name</th>
                <th scope="col">Category</th>
                <th scope="col">Stock</th>
                <th scope="col">Expired</th>
                <th scope="col">Price</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($products as $product)                  
                <tr>
                    <th scope="row"><a href="#">{{ $product->product_id }}</a></th>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->product_category }}</td>
                    <td>{{ $product->product_quantity }}</td>
                    <td>{{ date('d F Y', strtotime($product->product_expired)) }}</td>
                    <td>Rp{{ number_format($product->product_price) }}</td>
                    <td><button wire:click="addToCart('{{ $product->id }}')" class="btn badge bg-success">Sell</button></td>
                </tr>
              @endforeach
            </tbody>
          </table>

        </div>

      </div>
    </div>
    <!-- End Product Card -->

    <!-- Right side columns -->
    <div class="col-lg-6">
      <div class="card overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Sell Product</h5>
  
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Product</th>
                  <th scope="col">Price</th>
                  <th scope="col">Quantity</th>
                  <th scope="col">Sub Total</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($detail_pending as $pending)                 
                  <tr>
                    <th scope="row"><a href="#">{{ $pending->product_id }}</a></th>
                    <td>{{ $pending->product_name}}</td>
                    <td>Rp{{ number_format($pending->product_price) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <button
                                wire:click="decrementQuantity('{{ $pending->id }}')"
                                type="button"
                                class="btn btn-warning btn-sm px-2"
                            >-</button>
                    
                            <input
                                type="number"
                                min="1"
                                class="form-control form-control-sm text-center"
                                style="width: 50px;"
                                wire:model.lazy="quantities.{{ $pending->id }}"
                                wire:keydown.enter="updateQuantityManual({{ $pending->id }})"
                            />
                    
                            <button
                                wire:click="incrementQuantity('{{ $pending->id }}')"
                                type="button"
                                class="btn btn-success btn-sm px-2"
                            >+</button>
                        </div>
                    </td>
                    <td>Rp{{ number_format($pending->product_price * $pending->quantity) }}</td>
                    <td><button wire:click="removeFromCart('{{ $pending->id }}')" class="btn badge bg-danger">Delete</button></td>
                  </tr>
                @endforeach                
              </tbody>
            </table>

            @if (count($detail_pending) > 0)
              <div class="border-top pt-3">
                <div class="row mb-2">
                  <div class="col-md-6 text-end fw-bold">Amount:</div>
                  <div class="col-md-6 fw-bold">Rp {{ number_format($total) }}</div>
                </div>

                <div class="row mb-2 align-items-center">
                  <div class="col-md-6 text-end fw-bold">Payment Method:</div>
                  <div class="col-md-6">
                    <select class="form-select" wire:model="payment_method">
                      <option selected>Select Payment Method</option>
                      <option value="1">Cash</option>
                      <option value="2">Transfer</option>
                      <option value="3">QRIS</option>
                    </select>
                  </div>
                </div>

                <div class="row mb-2 align-items-center">
                  <div class="col-md-6 text-end fw-bold">Pay:</div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-text">Rp</span>
                      <input type="number" class="form-control" wire:model.live="pay" min="0">
                    </div>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-6 text-end fw-bold">Change:</div>
                  <div class="col-md-6 fw-bold">Rp {{ number_format($change) }}</div>
                </div>

                <div class="d-grid gap-2 mt-3">
                  <button 
                    class="btn badge bg-success" 
                    wire:click="sellProduct()"
                    @if($pay < $total || empty($payment_method)) disabled @endif
                  >
                    Checkout
                  </button>
                </div>
              </div>
            @endif
        </div>
      </div>
    </div>
    <!-- End Right side columns -->
</div>

<!-- JavaScript untuk handle cetak struk -->
<script>
document.addEventListener('livewire:init', function () {
    Livewire.on('print-receipt', function (data) {
        // Buka window baru untuk cetak struk
        const printWindow = window.open('/print-receipt/' + data[0].sale_id, '_blank', 'width=400,height=600,scrollbars=yes');
        
        // Auto print ketika window sudah loaded
        printWindow.onload = function() {
            setTimeout(function() {
                printWindow.print();
            }, 500);
        };
    });
});
</script>