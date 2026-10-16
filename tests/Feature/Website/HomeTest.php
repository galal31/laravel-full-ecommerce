<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_displays_active_catalog_sections(): void
    {
        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);

        $category = Category::query()->create([
            'name' => ['en' => 'Home category', 'ar' => 'فئة الرئيسية'],
            'slug' => 'home-category',
            'status' => true,
        ]);

        $brand = Brand::query()->create([
            'name' => ['en' => 'Home brand', 'ar' => 'براند الرئيسية'],
            'slug' => 'home-brand',
            'status' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => ['en' => 'Newest home product', 'ar' => 'أحدث منتج'],
            'status' => true,
            'sku' => 'HOME-1',
            'price' => 250,
            'manage_stock' => false,
            'available_in_stock' => true,
        ]);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee($brand->name)
            ->assertSee($product->name);
    }

    public function test_search_matches_both_languages_and_never_returns_inactive_products(): void
    {
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
        $category = Category::create(['name' => ['en' => 'Electronics'], 'slug' => 'search-electronics', 'status' => true]);
        $product = Product::create([
            'category_id' => $category->id, 'name' => ['ar' => 'سماعات لاسلكية', 'en' => 'Wireless headphones'],
            'sku' => 'SEARCH-1', 'price' => 150, 'status' => true, 'available_in_stock' => true,
        ]);
        $inactive = Product::create([
            'category_id' => $category->id, 'name' => ['ar' => 'سماعات مخفية', 'en' => 'Hidden headphones'],
            'sku' => 'SEARCH-2', 'price' => 150, 'status' => false,
        ]);

        foreach (['سماعات', 'headphones'] as $query) {
            $this->get(route('website.home', ['q' => $query]))->assertOk()
                ->assertSee($product->name)->assertDontSee($inactive->name)
                ->assertViewHas('searchProducts', fn ($products) => $products->modelKeys() === [$product->id]);
        }
    }

    public function test_no_matching_products_shows_a_clear_empty_state_and_escapes_the_query(): void
    {
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
        $query = '<script>alert(1)</script>';
        $this->get(route('website.home', ['q' => $query]))->assertOk()
            ->assertSee(__('home.no_search_results'))
            ->assertSee(e($query), false)
            ->assertDontSee($query, false);
    }

    public function test_home_hides_empty_deals_and_unavailable_checkout_routes(): void
    {
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
        $this->get(route('website.home'))->assertOk()
            ->assertDontSee('id="today-deals"', false)
            ->assertDontSee('checkout')
            ->assertSee('role="search"', false);
    }

    public function test_home_keeps_discount_and_variant_prices_and_renders_real_deals(): void
    {
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
        $category = Category::create(['name' => ['en' => 'Clothes'], 'slug' => 'home-clothes', 'status' => true]);
        $discounted = Product::create([
            'category_id' => $category->id, 'name' => ['en' => 'Discounted shirt'], 'sku' => 'DEAL-1',
            'price' => 200, 'discount' => 25, 'status' => true, 'available_in_stock' => true,
            'start_discount' => now()->subDay()->toDateString(), 'end_discount' => now()->toDateString(),
        ]);
        $variantProduct = Product::create([
            'category_id' => $category->id, 'name' => ['en' => 'Variant shirt'], 'sku' => 'VARIANT-1',
            'price' => 100, 'status' => true, 'available_in_stock' => true,
        ]);
        $variantProduct->variants()->create(['price' => 350, 'stock' => 2]);

        $this->get(route('website.home'))->assertOk()->assertSee('id="today-deals"', false)
            ->assertSee('150.00')->assertSee('200.00')->assertSee('350.00')
            ->assertSee(__('website.starting_from'))->assertSee(__('home.choose_options'))
            ->assertViewHas('discountEndingTodayProducts', fn ($products) => $products->modelKeys() === [$discounted->id]);
    }

    public function test_home_rejects_an_oversized_or_non_string_search_query(): void
    {
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
        $this->get(route('website.home', ['q' => str_repeat('a', 101)]))->assertSessionHasErrors('q');
        $this->get(route('website.home', ['q' => ['invalid']]))->assertSessionHasErrors('q');
    }

    public function test_home_wishlist_button_reflects_authenticated_toggle_results(): void
    {
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
        $category = Category::create(['name' => ['en' => 'Gifts'], 'slug' => 'home-gifts', 'status' => true]);
        $product = Product::create([
            'category_id' => $category->id, 'name' => ['en' => 'Favorite gift'], 'sku' => 'FAVORITE-1',
            'price' => 100, 'status' => true, 'available_in_stock' => true,
        ]);
        $this->actingAs(User::factory()->create());

        $this->postJson(route('website.wishlist.toggle'), ['product_id' => $product->id])->assertOk()->assertJsonPath('added', true);
        $this->get(route('website.home'))->assertOk()->assertSee('aria-pressed="true"', false)
            ->assertSee(__('website.remove_from_wishlist'));

        $this->postJson(route('website.wishlist.toggle'), ['product_id' => $product->id])->assertOk()->assertJsonPath('added', false);
        $this->get(route('website.home'))->assertOk()->assertSee(__('website.add_to_wishlist'));
    }
}
