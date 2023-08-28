<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\SetCartQuantityRequest;
use App\Http\Requests\DeleteFromCartRequest;
use App\Interfaces\CartRepositoryInterface;
class CartController extends Controller
{
    private CartRepositoryInterface $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getUserCart()
    {
        $user = auth()->user();
        $repositoryResult = $this->cartRepository->getUserCart($user);

        return response([
            'products' => $repositoryResult['products'],
            'discount' => $repositoryResult['discount']
        ], 200);
    }

    public function add(AddToCartRequest $request)
    {
        $validated = $request->validated();
        $this->cartRepository->add($validated['product_id']);

        return response(['message' => 'Product added successfully'], 201);
    }

    public function quantity(SetCartQuantityRequest $request)
    {
        $this->cartRepository->quantity($request->validated());
        return response(['message' => 'Cart updated successfully'], 200);
    }

    public function delete(DeleteFromCartRequest $request)
    {
        $validated = $request->validated();
        $this->cartRepository->delete($validated['product_id']);

        return response(['message' => 'Product removed successfully'], 200);
    }
}
