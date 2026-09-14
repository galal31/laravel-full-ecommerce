<?php

use Livewire\Component;

new class extends Component
{
    public string $user_name = '';
    public string $user_email = '';
    public string $user_phone = '';
    public string $street = '';
    public string $note = '';

    public function mount(): void
    {
        $this->user_name = (string) auth()->user()?->name;
        $this->user_email = (string) auth()->user()?->email;
    }
};
?>

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
        <livewire:general.address-dropdown :include-shipping="true" />
    </div>
</section>
