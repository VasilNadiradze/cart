<?php

namespace App\Repositories;

use App\Models\ProductGroup;
use App\Services\CartService;
use App\Interfaces\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function getUserCart($user)
    {
        $cart = $user->carts;
        $discountGroup = ProductGroup::first();

        return $this->cartService->getUserCart($cart, $discountGroup);
    }

    public function add(int $productId)
    {
        return auth()->user()->carts()->updateOrCreate(
            ['product_id' => $productId],
            ['quantity' => 1]
        );
    }

    public function quantity(array $cartData)
    {
        return auth()->user()->carts()->where('product_id', $cartData['product_id'])->update([
            'quantity' => $cartData['quantity']
        ]);
    }

    public function delete(int $productId)
    {
        return auth()->user()->carts()->where('product_id', $productId)->delete();
    }
}
