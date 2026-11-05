<?php

namespace Tests\Unit\Services;

use App\Models\CartItem;
use App\Models\Dashboard\Product;
use App\Models\Dashboard\ProductVariant;
use App\Reposetories\website\CheckoutRepository;
use App\Services\website\CheckoutService;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    public function test_it_calculates_an_item_total_using_the_discounted_product_price(): void
    {
        $product = new Product([
            'price' => 100,
            'discount' => 10,
        ]);

        $cartItem = new CartItem(['quantity' => 2]);
        $cartItem->setRelation('product', $product);
        $cartItem->setRelation('productVariant', null);

        $this->assertSame(180.0, $this->service()->calculateItemTotal($cartItem));
    }

    public function test_it_calculates_the_subtotal_using_variant_and_product_prices(): void
    {
        $product = new Product([
            'price' => 100,
            'discount' => 10,
        ]);

        $productItem = new CartItem(['quantity' => 2]);
        $productItem->setRelation('product', $product);
        $productItem->setRelation('productVariant', null);

        $variantItem = new CartItem(['quantity' => 2]);
        $variantItem->setRelation('product', $product);
        $variantItem->setRelation('productVariant', new ProductVariant(['price' => 120]));

        $subtotal = $this->service()->calculateSubtotal(collect([
            $productItem,
            $variantItem,
        ]));

        $this->assertSame(420.0, $subtotal);
    }

    public function test_it_calculates_the_total_without_discounting_shipping(): void
    {
        $total = $this->service()->calculateTotal(
            subtotal: 420,
            shippingPrice: 30,
            couponDiscount: 42
        );

        $this->assertSame(408.0, $total);
    }

    private function service(): CheckoutService
    {
        return new CheckoutService(
            $this->createMock(CheckoutRepository::class)
        );
    }
}
