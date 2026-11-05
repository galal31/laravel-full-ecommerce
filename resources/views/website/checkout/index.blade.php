@extends('layouts.website.userlayout')

@section('title', __('website.checkout_title'))

@section('content')
    <div class="checkout-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <style>
            .checkout-page-heading {
                background: #fffafe;
                border-bottom: 1px solid #f5e7f1;
                padding: 5rem 0;
                text-align: center;
            }

            .checkout-page-heading h1 {
                color: #232532;
                font-size: 3.6rem;
                margin: 0 0 1rem;
            }

            .checkout-page-heading p {
                color: #797979;
                font-size: 1.5rem;
                margin: 0;
            }

            .checkout-page-content {
                padding: 6rem 0;
            }

            .checkout-layout {
                align-items: start;
                display: grid;
                gap: 2.4rem;
                grid-template-columns: minmax(0, 1fr) 34rem;
            }

            .checkout-card {
                background: #fff;
                border: 1px solid #eee4ec;
                border-radius: 1.2rem;
                box-shadow: 0 1.2rem 3rem rgba(35, 37, 50, 0.06);
                padding: 2.8rem;
            }

            .checkout-card-heading {
                border-bottom: 1px solid #f0e5ed;
                margin-bottom: 2.4rem;
                padding-bottom: 1.8rem;
            }

            .checkout-card-heading h2 {
                color: #232532;
                font-size: 2.2rem;
                margin: 0 0 0.7rem;
            }

            .checkout-card-heading p {
                color: #797979;
                font-size: 1.35rem;
                line-height: 1.7;
                margin: 0;
            }

            .checkout-fields-grid {
                display: grid;
                gap: 1.8rem;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .checkout-field {
                margin: 0;
            }

            .checkout-field.is-wide {
                grid-column: 1 / -1;
            }

            .checkout-field label,
            .checkout-billing-card .form-group label {
                color: #232532;
                display: block;
                font-size: 1.35rem;
                font-weight: 700;
                margin-bottom: 0.8rem;
            }

            .checkout-field .form-control,
            .checkout-billing-card .form-control {
                border: 1px solid #e4d8e1;
                border-radius: 0.8rem;
                color: #232532;
                font-size: 1.4rem;
                min-height: 4.8rem;
                padding: 1rem 1.2rem;
                width: 100%;
            }

            .checkout-field textarea.form-control {
                min-height: 11rem;
                resize: vertical;
            }

            .checkout-field .form-control:focus,
            .checkout-billing-card .form-control:focus {
                border-color: #ae1c9a;
                box-shadow: 0 0 0 0.3rem rgba(174, 28, 154, 0.1);
                outline: none;
            }

            .checkout-address-fields {
                border-top: 1px solid #f0e5ed;
                margin-top: 2.4rem;
                padding-top: 2.4rem;
            }

            .checkout-billing-card .form-group {
                margin-bottom: 0;
                padding-inline: 0.8rem;
            }

            .checkout-order-summary-card {
                position: sticky;
                top: 2rem;
            }

            .checkout-summary-placeholder {
                align-items: center;
                background: #fffafe;
                border: 1px dashed #dfcddd;
                border-radius: 1rem;
                color: #797979;
                display: flex;
                font-size: 1.35rem;
                justify-content: center;
                line-height: 1.8;
                min-height: 18rem;
                padding: 2.4rem;
                text-align: center;
            }

            .checkout-summary-list {
                display: grid;
                gap: 1.6rem;
            }

            .checkout-summary-item {
                border-bottom: 1px solid #f0e5ed;
                display: grid;
                gap: 1.3rem;
                grid-template-columns: 6.4rem minmax(0, 1fr);
                padding-bottom: 1.6rem;
            }

            .checkout-summary-image {
                align-items: center;
                background: #fffafe;
                border-radius: 0.8rem;
                display: flex;
                height: 6.4rem;
                justify-content: center;
                overflow: hidden;
                width: 6.4rem;
            }

            .checkout-summary-image img {
                height: 100%;
                object-fit: contain;
                width: 100%;
            }

            .checkout-summary-info {
                min-width: 0;
            }

            .checkout-summary-name {
                color: #232532;
                display: block;
                font-size: 1.4rem;
                font-weight: 700;
                line-height: 1.5;
            }

            .checkout-summary-name:hover {
                color: #ae1c9a;
            }

            .checkout-summary-meta {
                color: #8a8994;
                font-size: 1.15rem;
                margin: 0.3rem 0 0.8rem;
            }

            .checkout-summary-price-row {
                align-items: center;
                display: flex;
                flex-wrap: wrap;
                gap: 0.8rem;
                justify-content: space-between;
            }

            .checkout-summary-quantity {
                color: #797979;
                font-size: 1.2rem;
            }

            .checkout-summary-price {
                color: #ae1c9a;
                font-size: 1.3rem;
                font-weight: 800;
                white-space: nowrap;
            }

            .checkout-summary-old-price {
                color: #999;
                font-size: 1.1rem;
                font-weight: 500;
                margin-inline-end: 0.4rem;
                text-decoration: line-through;
            }

            .checkout-summary-discount {
                background: #fff0fa;
                border-radius: 2rem;
                color: #ae1c9a;
                display: inline-flex;
                font-size: 1.05rem;
                font-weight: 700;
                margin-top: 0.8rem;
                padding: 0.4rem 0.8rem;
            }

            .checkout-coupon {
                background: #fffafe;
                border: 1px dashed #dcbfd6;
                border-radius: 1rem;
                margin-top: 2rem;
                padding: 1.6rem;
            }

            .checkout-coupon-heading {
                align-items: flex-start;
                display: flex;
                gap: 1.2rem;
                justify-content: space-between;
                margin-bottom: 1.3rem;
            }

            .checkout-coupon-heading h3 {
                color: #232532;
                font-size: 1.45rem;
                font-weight: 800;
                margin: 0 0 0.35rem;
            }

            .checkout-coupon-heading p {
                color: #797979;
                font-size: 1.15rem;
                line-height: 1.6;
                margin: 0;
            }

            .checkout-coupon-mark {
                align-items: center;
                background: #ae1c9a;
                border-radius: 50%;
                color: #fff;
                display: inline-flex;
                flex: 0 0 3.2rem;
                font-size: 1.35rem;
                font-weight: 800;
                height: 3.2rem;
                justify-content: center;
            }

            .checkout-coupon-form {
                display: grid;
                gap: 0.8rem;
                grid-template-columns: minmax(0, 1fr) auto;
            }

            .checkout-coupon-input {
                position: relative;
            }

            .checkout-coupon-form .form-control {
                border: 1px solid #e4d8e1;
                border-radius: 0.8rem;
                color: #232532;
                font-size: 1.3rem;
                min-height: 4.4rem;
                padding: 0.9rem 1.1rem;
                padding-inline-end: 4rem;
            }

            .checkout-coupon-form .form-control:focus {
                border-color: #ae1c9a;
                box-shadow: 0 0 0 0.3rem rgba(174, 28, 154, 0.1);
                outline: none;
            }

            .checkout-coupon-remove {
                align-items: center;
                background: #fff0f0;
                border: 0;
                border-radius: 50%;
                color: #c62828;
                cursor: pointer;
                display: inline-flex;
                font-size: 2rem;
                font-weight: 700;
                height: 2.8rem;
                inset-inline-end: 0.8rem;
                justify-content: center;
                line-height: 1;
                padding: 0;
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 2.8rem;
            }

            .checkout-coupon-remove:hover,
            .checkout-coupon-remove:focus-visible {
                background: #c62828;
                color: #fff;
                outline: none;
            }

            .checkout-coupon-button {
                background: #ae1c9a;
                border: 1px solid #ae1c9a;
                border-radius: 0.8rem;
                color: #fff;
                cursor: pointer;
                font-size: 1.25rem;
                font-weight: 700;
                min-height: 4.4rem;
                padding: 0.9rem 1.5rem;
            }

            .checkout-coupon-message {
                border-radius: 0.7rem;
                font-size: 1.15rem;
                margin: 0 0 1rem;
                padding: 0.8rem 1rem;
            }

            .checkout-coupon-message.is-success {
                background: #eaf8ef;
                color: #238636;
            }

            .checkout-coupon-message.is-error {
                background: #fff0f0;
                color: #c62828;
            }

            .checkout-coupon-applied {
                margin-top: 1.1rem;
            }

            .checkout-coupon-applied p {
                color: #238636;
                font-size: 1.15rem;
                font-weight: 700;
                margin: 0;
            }

            .checkout-summary-totals {
                display: grid;
                gap: 1.2rem;
                padding-top: 2rem;
            }

            .checkout-summary-total-row {
                align-items: center;
                display: flex;
                font-size: 1.35rem;
                justify-content: space-between;
            }

            .checkout-summary-total-row span {
                color: #565664;
                font-weight: 700;
            }

            .checkout-summary-total-row strong {
                color: #ae1c9a;
                font-size: 1.4rem;
            }

            .checkout-summary-total-row.is-coupon strong {
                color: #238636;
            }

            .checkout-summary-total-row.is-total {
                border-top: 1px solid #f0e5ed;
                margin-top: 0.4rem;
                padding-top: 1.4rem;
            }

            .checkout-summary-total-row.is-total strong {
                font-size: 1.8rem;
            }

            .checkout-place-order-button {
                align-items: center;
                background: #ae1c9a;
                border: 1px solid #ae1c9a;
                border-radius: 0.9rem;
                color: #fff;
                cursor: pointer;
                display: flex;
                font-size: 1.4rem;
                font-weight: 800;
                justify-content: center;
                margin-top: 2rem;
                min-height: 5rem;
                padding: 1rem 1.5rem;
                transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
                width: 100%;
            }

            .checkout-place-order-button:hover,
            .checkout-place-order-button:focus-visible {
                background: #8f177f;
                border-color: #8f177f;
                color: #fff;
                outline: none;
            }

            .checkout-place-order-button:active {
                transform: translateY(1px);
            }

            @media (max-width: 991px) {
                .checkout-layout {
                    grid-template-columns: 1fr;
                }

                .checkout-order-summary-card {
                    position: static;
                }
            }

            @media (max-width: 767px) {
                .checkout-page-heading {
                    padding: 3rem 0;
                }

                .checkout-page-heading h1 {
                    font-size: 2.8rem;
                }

                .checkout-page-content {
                    padding: 3.5rem 0;
                }

                .checkout-card {
                    padding: 2rem 1.5rem;
                }

                .checkout-fields-grid {
                    grid-template-columns: 1fr;
                }

                .checkout-billing-card .form-group {
                    margin-bottom: 1.6rem;
                    padding-inline: 0;
                }

                .checkout-coupon-form {
                    grid-template-columns: 1fr;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .checkout-field .form-control,
                .checkout-billing-card .form-control,
                .checkout-place-order-button {
                    scroll-behavior: auto;
                    transition: none;
                }
            }
        </style>

        <section class="checkout-page-heading">
            <div class="container">
                <h1>{{ __('website.checkout_title') }}</h1>
                <p>{{ __('website.checkout_subtitle') }}</p>
            </div>
        </section>

        <section class="checkout-page-content">
            <div class="container">
                @livewire('website.checkout.checkout-form', ['cartItems' => $cartItems])
            </div>
        </section>
    </div>
@endsection
