<?php

namespace App\Services;

class CartService
{
    public function getUserCart($cart, $discountGroup)
    {
        $discountIsActive = $this->discountIsActive($cart, $discountGroup);
        $discountedItems = [];
        $result = [];

        foreach($cart as $cartItem)
        {
            $resultItem = [
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price
            ];

            $result['products'][] = $resultItem;

            // if discount system is active & current iteration product is in discount
            if($discountIsActive && $cartItem->product->discountGroup->count())
            {
                $discountedItems[] = $resultItem;
            }
        }

        $result['discount'] = count($discountedItems) ?
                $this->getDiscount($discountedItems, $discountGroup) : 0;

        return $result;
    }

    public function discountIsActive($cart, $discountGroup)
    {
        // discount group's all product
        $groupProductIds = $discountGroup->products->pluck('id')->toArray();
        $cartProductIds = $cart->pluck('product_id')->toArray();

        // discount is active if all it's products added into cart
        return !count(array_diff($groupProductIds, $cartProductIds));
    }

    public function getDiscount($discountedItems, $discountGroup)
    {
        $discount = 0;
        $discountPercent = $discountGroup->discount;
        $minQuantity = min(array_column($discountedItems, 'quantity'));

        foreach ($discountedItems as $discountedItem)
        {
            $discountedAmountPerProduct = $discountedItem['price'] / 100 * $discountPercent;
            $discount += $discountedAmountPerProduct * $minQuantity;
        }

        return $discount;
    }
}
