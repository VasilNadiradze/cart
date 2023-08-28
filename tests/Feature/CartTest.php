<?php

namespace Tests\Feature;

use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use App\Models\ProductGroup;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_add_item_to_cart()
    {
        // create product owner
        $user = User::factory()->create();

        // create product
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'title' => fake()->word(),
            'price' => fake()->randomFloat(2, 10, 1000)
        ]);

        // create another user and log in
        Sanctum::actingAs(User::factory()->create(), ['*']);

        $response = $this->postJson("/api/add_to_cart", [
            'product_id' => $product->id
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('carts', [
            'user_id' => auth()->user()->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    /** @test */
    public function user_can_update_quantity_in_cart()
    {
        // create product owner
        $user = User::factory()->create();

        // create product
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'title' => fake()->word(),
            'price' => fake()->randomFloat(2, 10, 1000)
        ]);

        // create another user and log in
        Sanctum::actingAs(User::factory()->create(), ['*']);

        // add product to the logged in user's cart
        $cartItem = Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->postJson("/api/set_cart_quantity", [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('carts', [
            'user_id' => auth()->user()->id,
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);
    }

    /** @test */
    public function user_can_delete_item_from_cart()
    {
        // create product owner
        $user = User::factory()->create();

        // create product
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'title' => fake()->word(),
            'price' => fake()->randomFloat(2, 10, 1000)
        ]);

        // create another user and log in
        Sanctum::actingAs(User::factory()->create(), ['*']);

        // add product to the logged in user's cart
        $cartItem = Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->postJson("/api/delete_from_cart", [
            'product_id' => $product->id
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('carts', [
            'id' => $cartItem->id,
        ]);
    }

    /** @test */
    public function user_can_get_cart_items()
    {
        // create product owner
        $user = User::factory()->create();

        // create product
        $product1 = Product::factory()->create([
            'user_id' => $user->id,
            'title' => fake()->word(),
            'price' => 100
        ]);

        $product2 = Product::factory()->create([
            'user_id' => $user->id,
            'title' => fake()->word(),
            'price' => 200
        ]);

        $product3 = Product::factory()->create([
            'user_id' => $user->id,
            'title' => fake()->word(),
            'price' => 300
        ]);

        // create discount group
        $discountGroup = ProductGroup::create([
            'user_id' => $user->id,
            'discount' => 10
        ]);

        // attach first & second products to discount group
        DB::table('product_group_items')->insert([
            [
                'group_id' => $discountGroup->id,
                'product_id' => $product1->id,
            ],
            [
                'group_id' => $discountGroup->id,
                'product_id' => $product2->id,
            ]
        ]);

        // create another user and log in
        Sanctum::actingAs(User::factory()->create(), ['*']);

        // add first product into cart
        Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product1->id,
            'quantity' => 3,
        ]);

        // add second product into cart
        Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product2->id,
            'quantity' => 4,
        ]);

        // add third product into cart
        Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product3->id,
            'quantity' => 1,
        ]);

        $response = $this->getJson("/api/cart");

        $response
                ->assertStatus(200)
                ->assertJson([
                    "products" => [
                        [
                            "product_id" => $product1->id,
                            "quantity" => 3,
                            "price" => $product1->price
                        ],
                        [
                            "product_id" => $product2->id,
                            "quantity" => 4,
                            "price" => $product2->price
                        ],
                        [
                            "product_id" => $product3->id,
                            "quantity" => 1,
                            "price" => $product3->price
                        ]
                    ],
                    "discount" => 90
                ]);
    }
}
