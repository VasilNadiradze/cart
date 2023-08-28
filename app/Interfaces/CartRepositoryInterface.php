<?php

namespace App\Interfaces;

interface CartRepositoryInterface
{
    public function getUserCart($user);
    public function add(int $productId);
    public function quantity(array $cartData);
    public function delete(int $productId);
}
