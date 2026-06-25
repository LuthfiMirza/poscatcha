<?php

namespace Tests\Feature\OnlineOrdering;

use App\Models\BuyerCartItem;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\OnlineOrdering\BuyerCartService;
use App\Services\OnlineOrdering\OrderCheckoutService;
use App\Services\OnlineOrdering\OrderWorkflowService;
use App\Support\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OnlineOrderingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'buyer']);
        Role::firstOrCreate(['name' => 'cashier']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    public function test_checkout_creates_pending_order_and_does_not_reduce_stock(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $product = Product::factory()->create(['product_quantity' => 5, 'product_price' => 12000]);

        app(BuyerCartService::class)->add($buyer, $product, 2);
        $order = app(OrderCheckoutService::class)->checkout($buyer, Order::PAYMENT_CASH);

        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(24000, $order->total_price);
        $this->assertSame(5, $product->fresh()->product_quantity);
        $this->assertCount(1, $order->items);
        $this->assertDatabaseMissing('buyer_cart_items', ['product_id' => $product->product_id]);
    }

    public function test_login_register_and_guest_route_protection_are_available(): void
    {
        $this->get('/')->assertRedirect(route('buyer.shop.index'));
        $this->get('/login')->assertOk();
        $this->get(route('buyer.login'))->assertOk();
        $this->get('/register')->assertOk();
        $this->get(route('buyer.register'))->assertOk();
        $this->get(route('buyer.cart.index'))->assertRedirect(route('buyer.login'));
        $this->get(route('online-orders.index'))->assertRedirect('/login');
    }

    public function test_guest_can_browse_public_catalog_detail_search_and_filter_without_cart(): void
    {
        Category::create([
            'category_id' => 'CAT001',
            'category_name' => 'Minuman',
            'added_by' => 'test',
        ]);
        $product = Product::factory()->create([
            'product_name' => 'Matcha Latte',
            'product_category' => 'CAT001',
            'product_quantity' => 5,
        ]);
        $soldOut = Product::factory()->create([
            'product_name' => 'Sold Out Bottle',
            'product_category' => 'CAT001',
            'product_quantity' => 0,
        ]);

        $this->get(route('buyer.shop.index'))
            ->assertOk()
            ->assertSee('Matcha Latte')
            ->assertSee('Tambah ke Keranjang')
            ->assertSee('Beli Sekarang')
            ->assertSee('Sold Out Bottle')
            ->assertSee('Habis')
            ->assertDontSee(route('buyer.login.required', ['redirect_to' => route('buyer.products.show', $soldOut)]), false);

        $this->get(route('buyer.shop.index', ['q' => 'Matcha']))
            ->assertOk()
            ->assertSee('Matcha Latte');

        $this->get(route('buyer.shop.index', ['category' => 'CAT001']))
            ->assertOk()
            ->assertSee('Matcha Latte');

        $this->get(route('buyer.products.show', $product))
            ->assertOk()
            ->assertSee('Beli Sekarang')
            ->assertSee('Tambah ke Keranjang');

        $this->assertDatabaseCount('buyer_carts', 0);
        $this->assertDatabaseCount('buyer_cart_items', 0);
    }

    public function test_guest_purchase_actions_redirect_to_login_and_do_not_create_cart_or_order(): void
    {
        $product = Product::factory()->create(['product_quantity' => 5]);

        $this->post(route('buyer.cart.add', $product), ['quantity' => 1])->assertRedirect(route('buyer.login'));
        $this->get(route('buyer.cart.index'))->assertRedirect(route('buyer.login'));
        $this->get(route('buyer.checkout.index'))->assertRedirect(route('buyer.login'));
        $this->get(route('buyer.orders.index'))->assertRedirect(route('buyer.login'));

        $this->assertDatabaseCount('buyer_carts', 0);
        $this->assertDatabaseCount('buyer_cart_items', 0);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_login_required_route_stores_safe_intended_url_for_guest_purchase(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $product = Product::factory()->create(['product_quantity' => 5]);
        $productUrl = route('buyer.products.show', $product);

        $this->get(route('buyer.login.required', ['redirect_to' => $productUrl]))
            ->assertRedirect(route('buyer.login'))
            ->assertSessionHas('url.intended', $productUrl)
            ->assertSessionHas('error');

        $this->post(route('buyer.login.store'), [
            'email' => $buyer->email,
            'password' => 'password',
        ])->assertRedirect($productUrl);
    }

    public function test_buyer_auth_navigation_and_role_redirects_are_separated(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->get(route('buyer.shop.index'))
            ->assertOk()
            ->assertSee(route('buyer.login'), false)
            ->assertSee(route('buyer.register'), false)
            ->assertDontSee('Logout');

        $this->post(route('buyer.login.store'), [
            'email' => $buyer->email,
            'password' => 'password',
        ])->assertRedirect(route('buyer.shop.index'));

        $this->assertAuthenticatedAs($buyer);
        $this->post(route('logout'))->assertRedirect(route('buyer.shop.index'));

        $this->post(route('buyer.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard_admin'));

        auth()->logout();

        $this->post('/login', [
            'email' => $cashier->email,
            'password' => 'password',
        ])->assertRedirect(route('cashier.shift.open'));
    }

    public function test_cashier_and_admin_can_browse_public_catalog_but_cannot_use_buyer_cart_endpoint(): void
    {
        $product = Product::factory()->create(['product_quantity' => 5]);
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($cashier)->get(route('buyer.shop.index'))->assertOk();
        $this->actingAs($cashier)->post(route('buyer.cart.add', $product), ['quantity' => 1])->assertForbidden();

        $this->actingAs($admin)->get(route('buyer.products.show', $product))->assertOk();
        $this->actingAs($admin)->post(route('buyer.cart.add', $product), ['quantity' => 1])->assertForbidden();

        $this->assertDatabaseCount('buyer_cart_items', 0);
    }

    public function test_cart_add_same_product_increases_quantity_and_validates_stock(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $product = Product::factory()->create(['product_quantity' => 3]);

        app(BuyerCartService::class)->add($buyer, $product, 1);
        app(BuyerCartService::class)->add($buyer, $product, 2);

        $this->assertSame(3, BuyerCartItem::first()->quantity);
        $this->assertSame(1, BuyerCartItem::count());

        $this->expectException(ValidationException::class);
        app(BuyerCartService::class)->add($buyer, $product, 1);
    }

    public function test_buyer_can_use_buy_now_and_sold_out_product_is_not_added(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $product = Product::factory()->create(['product_quantity' => 5]);
        $soldOut = Product::factory()->create(['product_quantity' => 0]);

        $this->actingAs($buyer)
            ->post(route('buyer.cart.buy-now', $product), ['quantity' => 1])
            ->assertRedirect(route('buyer.checkout.index'));

        $this->assertSame(1, BuyerCartItem::count());

        $this->actingAs($buyer)
            ->post(route('buyer.cart.add', $soldOut), ['quantity' => 1])
            ->assertSessionHasErrors();

        $this->assertDatabaseMissing('buyer_cart_items', ['product_id' => $soldOut->product_id]);
    }

    public function test_buyer_cannot_update_another_buyers_cart_item(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $otherBuyer = User::factory()->create();
        $otherBuyer->assignRole('buyer');
        $product = Product::factory()->create(['product_quantity' => 5]);

        $item = app(BuyerCartService::class)->add($buyer, $product, 1);

        $this->actingAs($otherBuyer)
            ->patch(route('buyer.cart.items.update', $item), ['quantity' => 2])
            ->assertNotFound();

        $this->assertSame(1, $item->fresh()->quantity);
    }

    public function test_checkout_snapshots_price_and_name_and_second_checkout_with_empty_cart_fails(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $product = Product::factory()->create([
            'product_name' => 'Original Name',
            'product_quantity' => 5,
            'product_price' => 12000,
        ]);

        app(BuyerCartService::class)->add($buyer, $product, 2);
        $order = app(OrderCheckoutService::class)->checkout($buyer, PaymentMethod::TRANSFER, 'catatan');

        $this->assertSame('waiting_verification', $order->payment_status);
        $this->assertSame('pickup', $order->fulfillment_type);
        $this->assertNotEmpty($order->order_code);
        $this->assertSame('Original Name', $order->items->first()->product_name);
        $this->assertSame(12000, $order->items->first()->price);

        $this->expectException(ValidationException::class);
        app(OrderCheckoutService::class)->checkout($buyer, PaymentMethod::CASH);
    }

    public function test_checkout_rolls_back_when_one_item_has_insufficient_stock(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $first = Product::factory()->create(['product_quantity' => 5]);
        $second = Product::factory()->create(['product_quantity' => 2]);

        app(BuyerCartService::class)->add($buyer, $first, 1);
        app(BuyerCartService::class)->add($buyer, $second, 2);
        $second->update(['product_quantity' => 1]);

        try {
            app(OrderCheckoutService::class)->checkout($buyer, PaymentMethod::CASH);
        } catch (ValidationException) {
            $this->assertSame(0, Order::count());
            $this->assertSame(2, BuyerCartItem::count());

            return;
        }

        $this->fail('Checkout should fail when one item has insufficient stock.');
    }

    public function test_cashier_confirmation_deducts_stock_once_and_creates_stock_movement(): void
    {
        [$buyer, $cashier, $product, $order] = $this->makePendingOrder(quantity: 2, stock: 5);

        app(OrderWorkflowService::class)->confirm($order, $cashier);

        $this->assertSame(3, $product->fresh()->product_quantity);
        $this->assertSame(Order::STATUS_CONFIRMED, $order->fresh()->status);
        $movement = StockMovement::where('transaction_id', $order->order_code)->first();
        $this->assertNotNull($movement);
        $this->assertSame(5, $movement->quantity_before);
        $this->assertSame(3, $movement->quantity_after);
        $this->assertSame('online_order', $movement->source);
        $this->assertSame($cashier->id, $order->fresh()->confirmed_by);
        $this->assertNotNull($order->fresh()->confirmed_at);
        $this->assertNotNull($order->fresh()->stock_deducted_at);

        $this->expectException(ValidationException::class);
        app(OrderWorkflowService::class)->confirm($order->fresh(), $cashier);
    }

    public function test_confirmation_rolls_back_all_items_when_one_product_has_insufficient_stock(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');
        $first = Product::factory()->create(['product_quantity' => 5]);
        $second = Product::factory()->create(['product_quantity' => 2]);

        app(BuyerCartService::class)->add($buyer, $first, 2);
        app(BuyerCartService::class)->add($buyer, $second, 2);
        $order = app(OrderCheckoutService::class)->checkout($buyer, PaymentMethod::CASH);
        $second->update(['product_quantity' => 1]);

        try {
            app(OrderWorkflowService::class)->confirm($order, $cashier);
        } catch (ValidationException) {
            $this->assertSame(5, $first->fresh()->product_quantity);
            $this->assertSame(1, $second->fresh()->product_quantity);
            $this->assertSame(Order::STATUS_PENDING, $order->fresh()->status);
            $this->assertSame(0, StockMovement::where('transaction_id', $order->order_code)->count());

            return;
        }

        $this->fail('Confirmation should fail and rollback when one product has insufficient stock.');
    }

    public function test_cancel_confirmed_order_restores_stock_once(): void
    {
        [$buyer, $cashier, $product, $order] = $this->makePendingOrder(quantity: 2, stock: 5);
        $workflow = app(OrderWorkflowService::class);

        $workflow->confirm($order, $cashier);
        $workflow->cancel($order->fresh(), $cashier, 'Buyer request');

        $this->assertSame(5, $product->fresh()->product_quantity);
        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(2, StockMovement::where('transaction_id', $order->order_code)->count());

        $this->expectException(ValidationException::class);
        $workflow->cancel($order->fresh(), $cashier, 'Second cancel');
    }

    public function test_cancel_pending_does_not_create_stock_movement_and_buyer_ownership_is_enforced(): void
    {
        [$buyer, $cashier, $product, $order] = $this->makePendingOrder(quantity: 1, stock: 5);
        $otherBuyer = User::factory()->create();
        $otherBuyer->assignRole('buyer');

        $this->actingAs($otherBuyer)->get(route('buyer.orders.show', $order))->assertForbidden();
        $this->actingAs($otherBuyer)->post(route('buyer.orders.cancel', $order))->assertForbidden();

        $this->actingAs($buyer)->post(route('buyer.orders.cancel', $order))->assertRedirect();
        $this->assertSame(5, $product->fresh()->product_quantity);
        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(0, StockMovement::where('transaction_id', $order->order_code)->count());
    }

    public function test_processing_transitions_and_processing_cancel_requires_reason(): void
    {
        [$buyer, $cashier, $product, $order] = $this->makePendingOrder(quantity: 1, stock: 5);
        $workflow = app(OrderWorkflowService::class);

        $this->expectException(ValidationException::class);
        $workflow->startProcessing($order, $cashier);
    }

    public function test_confirmed_can_process_and_processing_requires_reason_to_cancel(): void
    {
        [$buyer, $cashier, $product, $order] = $this->makePendingOrder(quantity: 1, stock: 5);
        $workflow = app(OrderWorkflowService::class);

        $workflow->confirm($order, $cashier);
        $workflow->startProcessing($order->fresh(), $cashier);

        $this->assertSame(Order::STATUS_PROCESSING, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->processing_at);

        try {
            $workflow->cancel($order->fresh(), $cashier);
        } catch (ValidationException) {
            $this->assertSame(Order::STATUS_PROCESSING, $order->fresh()->status);
            $workflow->cancel($order->fresh(), $cashier, 'Produk tidak tersedia');
            $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
            $this->assertSame(5, $product->fresh()->product_quantity);

            return;
        }

        $this->fail('Processing cancellation should require a reason.');
    }

    public function test_processing_completion_creates_single_online_sale_and_detail_sales(): void
    {
        [$buyer, $cashier, $product, $order] = $this->makePendingOrder(quantity: 2, stock: 5, paymentMethod: PaymentMethod::QRIS);
        $product->update(['product_name' => 'Changed Name', 'product_price' => 99999]);
        CashierShift::create([
            'cashier_id' => $cashier->id,
            'shift_start' => now(),
            'opening_cash' => 100000,
            'status' => 'open',
        ]);
        $workflow = app(OrderWorkflowService::class);

        $workflow->confirm($order, $cashier);
        $workflow->startProcessing($order->fresh(), $cashier);
        $workflow->complete($order->fresh(), $cashier);

        $sale = Sale::where('order_id', $order->id)->first();
        $this->assertNotNull($sale);
        $this->assertSame('online', $sale->source);
        $this->assertSame(PaymentMethod::SALES_QRIS, $sale->payment_method);
        $this->assertSame($order->total_price, $sale->total);
        $this->assertSame($order->total_price, $sale->pay);
        $this->assertSame(0, $sale->change);
        $this->assertSame(1, Sale::where('order_id', $order->id)->count());
        $this->assertDatabaseHas('detail_sales', [
            'sale_id' => $sale->sale_id,
            'product_id' => $product->product_id,
            'product_name' => $order->items->first()->product_name,
            'product_price' => $order->items->first()->price,
        ]);
        $this->assertSame(Order::STATUS_COMPLETED, $order->fresh()->status);

        $this->expectException(ValidationException::class);
        $workflow->complete($order->fresh(), $cashier);
    }

    public function test_completion_requires_cashier_shift_but_admin_can_complete_with_null_shift(): void
    {
        [$buyer, $cashier, $product, $order] = $this->makePendingOrder(quantity: 1, stock: 5);
        $workflow = app(OrderWorkflowService::class);
        $workflow->confirm($order, $cashier);
        $workflow->startProcessing($order->fresh(), $cashier);

        try {
            $workflow->complete($order->fresh(), $cashier);
        } catch (ValidationException) {
            $this->assertSame(0, Sale::count());
        }

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $workflow->complete($order->fresh(), $admin);

        $this->assertSame(Order::STATUS_COMPLETED, $order->fresh()->status);
        $this->assertNull(Sale::where('order_id', $order->id)->first()->shift_id);
    }

    public function test_payment_method_mapping_and_offline_sale_defaults_are_compatible(): void
    {
        $this->assertSame(PaymentMethod::SALES_CASH, PaymentMethod::toSales(PaymentMethod::CASH));
        $this->assertSame(PaymentMethod::SALES_TRANSFER, PaymentMethod::toSales(PaymentMethod::TRANSFER));
        $this->assertSame(PaymentMethod::SALES_QRIS, PaymentMethod::toSales(PaymentMethod::QRIS));

        $sale = Sale::create([
            'sale_id' => Sale::generateInvoiceNumber(),
            'cashier_id' => '1',
            'total' => 10000,
            'payment_method' => PaymentMethod::SALES_CASH,
            'pay' => 10000,
            'change' => 0,
        ]);

        $this->assertSame('offline', $sale->fresh()->source);
        $this->assertNull($sale->fresh()->order_id);
    }

    private function makePendingOrder(int $quantity, int $stock, string $paymentMethod = PaymentMethod::CASH): array
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');
        $product = Product::factory()->create(['product_quantity' => $stock, 'product_price' => 10000]);

        app(BuyerCartService::class)->add($buyer, $product, $quantity);
        $order = app(OrderCheckoutService::class)->checkout($buyer, $paymentMethod);

        return [$buyer, $cashier, $product, $order];
    }
}
