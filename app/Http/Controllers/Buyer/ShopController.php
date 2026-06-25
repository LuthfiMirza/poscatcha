<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\BuyerCartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\OnlineOrdering\BuyerCartService;
use App\Services\OnlineOrdering\OrderCheckoutService;
use App\Services\OnlineOrdering\OrderWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function loginRequired(Request $request): RedirectResponse
    {
        $redirectTo = $request->query('redirect_to', route('buyer.shop.index'));
        $appUrl = url('/');

        if (! is_string($redirectTo) || ! str_starts_with($redirectTo, $appUrl.'/shop')) {
            $redirectTo = route('buyer.shop.index');
        }

        $request->session()->put('url.intended', $redirectTo);

        return redirect()->route('buyer.login')->with('error', 'Silakan masuk sebagai pembeli untuk melanjutkan.');
    }

    public function index(Request $request): View
    {
        $search = Str::of((string) $request->query('q', ''))->trim()->limit(80, '')->toString();
        $category = (string) $request->query('category', '');
        $categories = Category::query()->orderBy('category_name')->get();

        if ($category !== '' && ! $categories->contains('category_id', $category)) {
            $category = '';
        }

        $products = Product::query()
            ->with(['category', 'recipes.rawMaterial'])
            ->when($search !== '', fn ($query) => $query->where('product_name', 'like', '%'.$search.'%'))
            ->when($category !== '', fn ($query) => $query->where('product_category', $category))
            ->orderBy('product_name')
            ->paginate(12)
            ->withQueryString();

        return view('buyer.shop.index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'cartCount' => $request->user()?->hasRole('buyer') ? app(BuyerCartService::class)->count($request->user()) : 0,
        ]);
    }

    public function show(Request $request, Product $product): View
    {
        $product->loadMissing(['category', 'recipes.rawMaterial']);

        return view('buyer.shop.show', [
            'product' => $product,
            'addOnProducts' => Product::query()
                ->with('recipes.rawMaterial')
                ->where('product_category', 'ADDON')
                ->where('product_id', '!=', $product->product_id)
                ->orderBy('product_name')
                ->get(),
            'cartCount' => $request->user()?->hasRole('buyer') ? app(BuyerCartService::class)->count($request->user()) : 0,
        ]);
    }

    public function addToCart(Request $request, Product $product, BuyerCartService $cartService): RedirectResponse
    {
        $validated = $this->validateCartCustomization($request);
        $cartService->add($request->user(), $product, (int) ($validated['quantity'] ?? 1), $this->customizationFrom($validated));

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function buyNow(Request $request, Product $product, BuyerCartService $cartService): RedirectResponse
    {
        $validated = $this->validateCartCustomization($request);
        $cartService->add($request->user(), $product, (int) ($validated['quantity'] ?? 1), $this->customizationFrom($validated));

        return redirect()->route('buyer.checkout.index')->with('success', 'Produk ditambahkan. Silakan lanjut checkout.');
    }

    protected function validateCartCustomization(Request $request): array
    {
        return $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'ice_level' => ['nullable', 'string', 'in:No Ice,Less Ice,Normal Ice,Extra Ice'],
            'sugar_level' => ['nullable', 'string', 'in:No Sugar,Less Sugar,Normal Sugar,Extra Sugar'],
            'add_ons' => ['nullable', 'array'],
            'add_ons.*' => ['string', 'max:50'],
        ]);
    }

    protected function customizationFrom(array $validated): array
    {
        return [
            'ice_level' => $validated['ice_level'] ?? 'Normal Ice',
            'sugar_level' => $validated['sugar_level'] ?? 'Normal Sugar',
            'add_ons' => $validated['add_ons'] ?? [],
        ];
    }

    public function cart(Request $request, BuyerCartService $cartService): View
    {
        $cart = $cartService->cartFor($request->user())->load('items.product');

        return view('buyer.shop.cart', [
            'cart' => $cart,
            'cartCount' => $cartService->count($request->user()),
        ]);
    }

    public function updateCartItem(Request $request, BuyerCartItem $item, BuyerCartService $cartService): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $cartService->update($request->user(), $item, (int) $validated['quantity']);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function removeCartItem(Request $request, BuyerCartItem $item, BuyerCartService $cartService): RedirectResponse
    {
        $cartService->remove($request->user(), $item);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function checkoutForm(Request $request, BuyerCartService $cartService): View
    {
        $cart = $cartService->cartFor($request->user())->load('items.product');

        return view('buyer.shop.checkout', [
            'cart' => $cart,
            'cartCount' => $cartService->count($request->user()),
        ]);
    }

    public function checkout(Request $request, OrderCheckoutService $checkoutService): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:cash,transfer,qris'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $order = $checkoutService->checkout($request->user(), $validated['payment_method'], $validated['note'] ?? null);

        return redirect()->route('buyer.orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
    }

    public function orders(Request $request): View
    {
        $group = $request->query('group', 'active');
        $statuses = match ($group) {
            'completed' => [Order::STATUS_COMPLETED],
            'cancelled' => [Order::STATUS_CANCELLED],
            default => [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_PROCESSING],
        };

        return view('buyer.orders.index', [
            'orders' => Order::where('user_id', $request->user()->id)->whereIn('status', $statuses)->latest()->paginate(10)->withQueryString(),
            'group' => $group,
            'cartCount' => app(BuyerCartService::class)->count($request->user()),
        ]);
    }

    public function orderShow(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return view('buyer.orders.show', [
            'order' => $order->load('items', 'confirmer', 'completer', 'canceller'),
            'cartCount' => app(BuyerCartService::class)->count($request->user()),
        ]);
    }

    public function cancelOrder(Request $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'Pembeli hanya dapat membatalkan pesanan pending.');
        }

        $workflow->cancel($order, $request->user(), 'Dibatalkan oleh pembeli');

        return back()->with('success', 'Pesanan dibatalkan.');
    }
}
