<?php

use App\Models\CartItem;
use App\Models\Dashboard\Coupon;
use App\Models\Dashboard\Country;
use App\Models\Dashboard\Governorate;
use App\Models\Dashboard\City;
use App\Services\website\CheckoutService;
use App\Services\website\CouponService;
use Livewire\Component;

new class extends Component
{
    public $cartItems;
    public string $user_name = '';
    public string $user_email = '';
    public string $user_phone = '';
    public string $street = '';
    public string $note = '';
    public $country_id = null;
    public $governorate_id = null;
    public $city_id = null;
    public float $shippingPrice = 0;
    public string $couponCode = '';
    public ?string $appliedCouponCode = null;
    public ?int $appliedCouponPercentage = null;
    public float $couponDiscount = 0;
    public ?Coupon $getAppliedCoupon = null;
    public ?string $couponSuccessMessage = null;
    public ?string $couponErrorMessage = null;
    protected CheckoutService $checkoutService;
    protected CouponService $couponService;

    // حقن خدمات حساب التشيك أوت والتعامل مع الكوبون عند تشغيل المكوّن.
    public function boot(
        CheckoutService $checkoutService,
        CouponService $couponService
    ): void {
        $this->checkoutService = $checkoutService;
        $this->couponService = $couponService;
    }


    public function mount($cartItems)
    {
        $this->cartItems = $cartItems;
        $this->user_name = (string) old('user_name', auth()->user()?->name);
        $this->user_email = (string) old('user_email', auth()->user()?->email);
        $this->user_phone = (string) old('user_phone', '');
        $this->street = (string) old('street', '');
        $this->note = (string) old('note', '');
        $this->country_id = old('country_id') ?: null;
        $this->governorate_id = old('governorate_id') ?: null;
        $this->city_id = old('city_id') ?: null;
        $this->shippingPrice = $this->selectedGovernorateShippingPrice();
        $this->getAppliedCoupon = $this->couponService->getAppliedCoupon((int) auth()->id());
        if ($this->getAppliedCoupon) {
            $this->couponCode = (string) $this->getAppliedCoupon->code;
            $this->appliedCouponCode = (string) $this->getAppliedCoupon->code;
            $this->appliedCouponPercentage = (int) $this->getAppliedCoupon->discount_percentage;
            $this->couponDiscount = $this->couponService->calculateDiscount($this->subtotal(), $this->getAppliedCoupon->discount_percentage);
        }
    }

    public function updatedCountryId(): void
    {
        $this->governorate_id = null;
        $this->city_id = null;
        $this->shippingPrice = 0;
    }

    public function updatedGovernorateId(): void
    {
        $this->city_id = null;

        $this->shippingPrice = $this->selectedGovernorateShippingPrice();
    }

    private function selectedGovernorateShippingPrice(): float
    {
        $governorate = Governorate::query()
            ->whereKey($this->governorate_id)
            ->where('country_id', $this->country_id)
            ->where('status', 1)
            ->with('shippingGovernorate')
            ->first();

        return (float) ($governorate?->shippingGovernorate?->price ?? 0);
    }

    public function with(): array
    {
        return [
            'countries' => Country::where('status', 1)->get(),
            'governorates' => $this->country_id
                ? Governorate::where('country_id', $this->country_id)->where('status', 1)->get()
                : [],
            'cities' => $this->governorate_id
                ? City::where('governorate_id', $this->governorate_id)->where('status', 1)->get()
                : [],
        ];
    }

    public function applyCoupon(): void
    {
        try {
            $coupon = $this->couponService->applyCoupon(
                (int) auth()->id(),
                $this->couponCode
            );

            $this->couponCode = (string) $coupon->code;
            $this->appliedCouponCode = (string) $coupon->code;
            $this->appliedCouponPercentage = (int) $coupon->discount_percentage;
            $this->couponDiscount = $this->couponService->calculateDiscount($this->subtotal(), $coupon->discount_percentage);
            $this->couponSuccessMessage = __('website.coupon_applied_successfully');
            $this->couponErrorMessage = null;
        } catch (\DomainException $exception) {
            $this->couponErrorMessage = $exception->getMessage();
            $this->couponSuccessMessage = null;
        }
    }

    public function removeCoupon(): void
    {
        $this->couponService->removeCoupon((int) auth()->id());
        $this->couponCode = '';
        $this->appliedCouponCode = null;
        $this->appliedCouponPercentage = null;
        $this->couponDiscount = 0;
        $this->getAppliedCoupon = null;
        $this->couponSuccessMessage = null;
        $this->couponErrorMessage = null;
    }

    public function itemTotal(CartItem $cartItem): float
    {
        return $this->checkoutService->calculateItemTotal($cartItem);
    }

    public function subtotal(): float
    {
        return $this->checkoutService->calculateSubtotal($this->cartItems);
    }


    public function total(): float
    {
        return $this->checkoutService->calculateTotal(
            $this->subtotal(),
            $this->shippingPrice,
            $this->couponDiscount
        );
    }
};
?>

<form class="checkout-layout" method="POST" action="{{ route('website.checkout.place-order') }}">
    @csrf
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" style="grid-column: 1 / -1;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <section class="checkout-card checkout-billing-card">
        <div class="checkout-card-heading">
            <h2>{{ __('website.checkout_billing_details') }}</h2>
            <p>{{ __('website.checkout_billing_hint') }}</p>
        </div>

        <div class="checkout-fields-grid">
            <div class="checkout-field">
                <label for="checkout-user-name">{{ __('website.checkout_full_name') }}</label>
                <input
                    type="text"
                    id="checkout-user-name"
                    name="user_name"
                    class="form-control"
                    placeholder="{{ __('website.checkout_full_name_placeholder') }}"
                    wire:model="user_name"
                    autocomplete="name"
                >
            </div>

            <div class="checkout-field">
                <label for="checkout-user-email">{{ __('website.checkout_email') }}</label>
                <input
                    type="email"
                    id="checkout-user-email"
                    name="user_email"
                    class="form-control"
                    placeholder="{{ __('website.checkout_email_placeholder') }}"
                    wire:model="user_email"
                    autocomplete="email"
                >
            </div>

            <div class="checkout-field">
                <label for="checkout-user-phone">{{ __('website.checkout_phone') }}</label>
                <input
                    type="tel"
                    id="checkout-user-phone"
                    name="user_phone"
                    class="form-control"
                    placeholder="{{ __('website.checkout_phone_placeholder') }}"
                    wire:model="user_phone"
                    autocomplete="tel"
                >
            </div>

            <div class="checkout-field">
                <label for="checkout-street">{{ __('website.checkout_street') }}</label>
                <input
                    type="text"
                    id="checkout-street"
                    name="street"
                    class="form-control"
                    placeholder="{{ __('website.checkout_street_placeholder') }}"
                    wire:model="street"
                    autocomplete="street-address"
                >
            </div>

            <div class="checkout-field is-wide">
                <label for="checkout-note">{{ __('website.checkout_note') }}</label>
                <textarea
                    id="checkout-note"
                    name="note"
                    class="form-control"
                    placeholder="{{ __('website.checkout_note_placeholder') }}"
                    wire:model="note"
                ></textarea>
            </div>
        </div>

        <div class="checkout-address-fields">
            <div class="row w-100 m-0">
                <div class="col-md-4 form-group pl-0">
                    <label for="checkout-country">{{ __('users.country') }}</label>
                    <select id="checkout-country" name="country_id" wire:model.live="country_id" class="form-control" required>
                        <option value="">{{ __('users.select_country') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->getTranslation('name', app()->getLocale()) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group">
                    <label for="checkout-governorate">{{ __('users.governorate') }}</label>
                    <select id="checkout-governorate" name="governorate_id" wire:model.live="governorate_id" class="form-control" required>
                        <option value="">{{ __('users.select_governorate') }}</option>
                        @foreach ($governorates as $governorate)
                            <option value="{{ $governorate->id }}">{{ $governorate->getTranslation('name', app()->getLocale()) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group pr-0">
                    <label for="checkout-city">{{ __('users.city') }}</label>
                    <select id="checkout-city" name="city_id" wire:model.live="city_id" class="form-control" required>
                        <option value="">{{ __('users.select_city') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->getTranslation('name', app()->getLocale()) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </section>

<aside class="checkout-card checkout-order-summary-card">
    <div class="checkout-card-heading">
        <h2>{{ __('website.checkout_order_summary') }}</h2>
        <p>{{ __('website.checkout_order_summary_hint') }}</p>
    </div>

    @if ($cartItems->isNotEmpty())
        <div class="checkout-summary-list">
            @foreach ($cartItems as $cartItem)
                @php
                    $product = $cartItem->product;
                    $variant = $cartItem->productVariant;
                    $hasDiscount = ! $variant && $product->hasActiveDiscount();
                    $lineTotal = $this->itemTotal($cartItem);
                    $variantName = $variant?->attributeValues
                        ->pluck('value')
                        ->filter()
                        ->join(' | ');
                    $image = $variant?->images->first()
                        ?? $product->images->first();
                @endphp

                <article class="checkout-summary-item" wire:key="checkout-item-{{ $cartItem->id }}">
                    <a
                        href="{{ route('website.products.show', $product->slug) }}"
                        class="checkout-summary-image"
                    >
                        <img
                            src="{{ $image?->image_url ?? asset('website-assets/assets/images/homepage-one/product-img/product-img-1.webp') }}"
                            alt="{{ $product->name }}"
                            loading="lazy"
                            decoding="async"
                        >
                    </a>

                    <div class="checkout-summary-info">
                        <a
                            href="{{ route('website.products.show', $product->slug) }}"
                            class="checkout-summary-name"
                        >
                            {{ $product->name }}
                        </a>

                        <p class="checkout-summary-meta">
                            {{ $variantName ?: ($product->brand?->name ?? __('website.no_brand')) }}
                        </p>

                        <div class="checkout-summary-price-row">
                            <span class="checkout-summary-quantity">
                                {{ __('website.quantity') }}: {{ $cartItem->quantity }}
                            </span>

                            <span class="checkout-summary-price">
                                @if ($hasDiscount)
                                    <span class="checkout-summary-old-price">
                                        {{ number_format((float) $product->price * $cartItem->quantity, 2) }}
                                    </span>
                                @endif

                                {{ number_format($lineTotal, 2) }}
                                {{ __('website.currency') }}
                            </span>
                        </div>

                        @if ($hasDiscount)
                            <span class="checkout-summary-discount">
                                {{ __('website.discount_badge', ['percentage' => (float) $product->discount]) }}
                            </span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <section class="checkout-coupon" aria-labelledby="checkout-coupon-title">
            <div class="checkout-coupon-heading">
                <div>
                    <h3 id="checkout-coupon-title">{{ __('website.checkout_coupon_title') }}</h3>
                    <p>{{ __('website.checkout_coupon_hint') }}</p>
                </div>

                <span class="checkout-coupon-mark" aria-hidden="true">%</span>
            </div>

            @if ($couponSuccessMessage)
                <p class="checkout-coupon-message is-success" role="status">
                    {{ $couponSuccessMessage }}
                </p>
            @endif

            @if ($couponErrorMessage)
                <p class="checkout-coupon-message is-error" role="alert">
                    {{ $couponErrorMessage }}
                </p>
            @endif

            <div class="checkout-coupon-form">
                <div class="checkout-coupon-input">
                    <input
                        type="text"
                        id="checkout-coupon-code"
                        class="form-control"
                        placeholder="{{ __('website.checkout_coupon_placeholder') }}"
                        wire:model="couponCode"
                        wire:keydown.enter.prevent="applyCoupon"
                        autocomplete="off"
                    >

                    @if ($appliedCouponCode || $getAppliedCoupon !== null)
                        <button
                            type="button"
                            class="checkout-coupon-remove"
                            wire:click="removeCoupon"
                            aria-label="{{ __('website.checkout_coupon_remove') }}"
                            title="{{ __('website.checkout_coupon_remove') }}"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    @endif
                </div>

                <button type="button" wire:click="applyCoupon" class="checkout-coupon-button">
                    {{ __('website.checkout_coupon_apply') }}
                </button>
            </div>

            @if ($appliedCouponCode)
                <div class="checkout-coupon-applied">
                    <p>
                        {{ __('website.checkout_coupon_applied', [
                            'code' => $appliedCouponCode,
                            'percentage' => $appliedCouponPercentage,
                        ]) }}
                    </p>
                </div>
            @endif
        </section>

        <div class="checkout-summary-totals">
            <div class="checkout-summary-total-row">
                <span>{{ __('website.cart_subtotal') }}</span>
                <strong>
                    {{ number_format($this->subtotal(), 2) }}
                    {{ __('website.currency') }}
                </strong>
            </div>

            <div class="checkout-summary-total-row">
                <span>{{ __('governorates.shipping_price') }}</span>
                <strong>
                    {{ number_format($shippingPrice, 2) }}
                    {{ __('website.currency') }}
                </strong>
            </div>

            @if ($couponDiscount > 0)
                <div class="checkout-summary-total-row is-coupon">
                    <span>{{ __('website.checkout_coupon_discount') }}</span>
                    <strong>
                        -{{ number_format($couponDiscount, 2) }}
                        {{ __('website.currency') }}
                    </strong>
                </div>
            @endif

            <div class="checkout-summary-total-row is-total">
                <span>{{ __('website.cart_total') }}</span>
                <strong>
                    {{ number_format($this->total(), 2) }}
                    {{ __('website.currency') }}
                </strong>
            </div>
        </div>

        <button type="submit" class="checkout-place-order-button">
            {{ __('website.checkout_place_order') }}
        </button>
    @else
        <div class="checkout-summary-placeholder">
            {{ __('website.cart_empty') }}
        </div>
    @endif
</aside>
</form>
