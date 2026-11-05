<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Order;
use App\Models\User;
use App\Services\website\MyFatoorahService;
use App\Services\website\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PlaceOrderValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_order_validates_customer_address_before_creating_the_order(): void
    {
        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);

        $this->actingAs(User::factory()->create());

        $countryId = $this->createCountry('Egypt');
        $otherCountryId = $this->createCountry('Other');
        $governorateId = $this->createGovernorate($countryId, 'Cairo');
        $otherGovernorateId = $this->createGovernorate($otherCountryId, 'Other');
        $cityId = $this->createCity($governorateId, 'Nasr City');
        $otherCityId = $this->createCity($otherGovernorateId, 'Other City');

        $data = [
            'user_name' => 'Test Customer',
            'user_email' => 'customer@example.com',
            'user_phone' => '01012345678',
            'street' => 'Main Street',
            'note' => '',
            'country_id' => $countryId,
            'governorate_id' => $governorateId,
            'city_id' => $cityId,
        ];

        $order = new Order;
        $this->mock(OrderService::class)->shouldReceive('createOrder')->once()
            ->withArgs(fn (array $validated) => $validated['governorate_id'] === $governorateId)
            ->andReturn($order);
        $this->mock(MyFatoorahService::class)->shouldReceive('createInvoice')->once()
            ->with($order)->andReturn(['payment_url' => 'https://payment.example.test']);

        $this->post(route('website.checkout.place-order'), $data)
            ->assertRedirect('https://payment.example.test');

        $this->post(route('website.checkout.place-order'), [
            ...$data,
            'city_id' => $otherCityId,
        ])->assertSessionHasErrors('city_id');

        $this->post(route('website.checkout.place-order'), [
            ...$data,
            'governorate_id' => $otherGovernorateId,
        ])->assertSessionHasErrors('governorate_id');

        $this->post(route('website.checkout.place-order'), [
            ...$data,
            'user_name' => '',
        ])->assertSessionHasErrors('user_name');
    }

    private function createCountry(string $name): int
    {
        return DB::table('countries')->insertGetId([
            'name' => json_encode(['en' => $name, 'ar' => $name]),
            'phone_code' => '+20',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createGovernorate(int $countryId, string $name): int
    {
        return DB::table('governorates')->insertGetId([
            'country_id' => $countryId,
            'name' => json_encode(['en' => $name, 'ar' => $name]),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createCity(int $governorateId, string $name): int
    {
        return DB::table('cities')->insertGetId([
            'governorate_id' => $governorateId,
            'name' => json_encode(['en' => $name, 'ar' => $name]),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
