<?php

namespace App\Livewire;

use App\Models\BuyerCartItem;
use App\Services\OnlineOrdering\BuyerCartService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class BuyerCartPage extends Component
{
    public ?string $errorMessage = null;

    public function increment(int $itemId): void
    {
        $item = $this->cartItem($itemId);

        if (! $item) {
            return;
        }

        $this->updateQuantity($item, $item->quantity + 1);
    }

    public function decrement(int $itemId): void
    {
        $item = $this->cartItem($itemId);

        if (! $item) {
            return;
        }

        if ($item->quantity <= 1) {
            $this->cartService()->remove(auth()->user(), $item);
            $this->errorMessage = null;

            return;
        }

        $this->updateQuantity($item, $item->quantity - 1);
    }

    public function setQuantity(int $itemId, mixed $quantity): void
    {
        $item = $this->cartItem($itemId);

        if (! $item) {
            return;
        }

        $this->updateQuantity($item, max(1, (int) $quantity));
    }

    public function remove(int $itemId): void
    {
        $item = $this->cartItem($itemId);

        if (! $item) {
            return;
        }

        $this->cartService()->remove(auth()->user(), $item);
        $this->errorMessage = null;
    }

    public function render()
    {
        $cart = $this->cartService()->cartFor(auth()->user())
            ->load('items.product.recipes.rawMaterial');

        return view('livewire.buyer-cart-page', [
            'cart' => $cart,
        ]);
    }

    protected function cartItem(int $itemId): ?BuyerCartItem
    {
        return BuyerCartItem::query()
            ->whereKey($itemId)
            ->whereHas('cart', fn ($query) => $query->where('user_id', auth()->id()))
            ->with('product.recipes.rawMaterial')
            ->first();
    }

    protected function updateQuantity(BuyerCartItem $item, int $quantity): void
    {
        try {
            $this->cartService()->update(auth()->user(), $item, $quantity);
            $this->errorMessage = null;
        } catch (ValidationException $exception) {
            $this->errorMessage = collect($exception->errors())->flatten()->first() ?? 'Quantity tidak valid.';
        }
    }

    protected function cartService(): BuyerCartService
    {
        return app(BuyerCartService::class);
    }
}
