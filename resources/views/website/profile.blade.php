@extends('layouts.website.userlayout')

@section('title', __('site_header.user_dashboard'))

@push('styles')
    <style>
        .account-dashboard {
            padding-block: 4rem 8rem;
        }

        .account-dashboard .dashboard-heading .dashboard-title {
            font-size: 2.8rem;
            margin: 0;
        }

        .account-dashboard-grid {
            --bs-gutter-x: 2.4rem;
            --bs-gutter-y: 2.4rem;
        }

        .account-dashboard-menu {
            display: grid;
            gap: .6rem;
        }

        .account-dashboard-tab {
            min-height: 4.6rem;
            padding: .9rem 1.2rem;
            border: 1px solid #e8e8e8;
            border-radius: .6rem;
            background: #fff;
            color: #232532;
            font-size: 1.6rem;
            text-align: start;
        }

        .account-dashboard-tab:hover,
        .account-dashboard-tab:focus-visible {
            border-color: #ae1c9a;
            background: #fbf0f8;
            color: #ae1c9a;
        }

        .account-dashboard-tab:focus-visible {
            outline: 2px solid #ae1c9a;
            outline-offset: 2px;
        }

        .account-dashboard-panel {
            height: 100%;
            min-height: 24rem;
            padding: 2rem;
            border: 1px solid #e8e8e8;
            border-radius: .8rem;
            background: #f8f8f8;
        }

        .account-overview {
            display: grid;
            gap: 1.6rem;
            color: #232532;
            font-size: 1.5rem;
        }

        .account-overview-welcome,
        .account-overview-card {
            min-width: 0;
            padding: 1.6rem;
            border: 1px solid #e5e7eb;
            border-radius: .8rem;
            background: #fff;
        }

        .account-overview-welcome {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.2rem;
            border-color: #ecd9e9;
            background: #fbf0f8;
        }

        .account-overview-user {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .account-overview h2 {
            margin-block-end: .6rem;
            font-size: 2.4rem;
        }

        .account-overview h3 {
            margin: 0;
            font-size: 1.8rem;
        }

        .account-overview :is(p, address, time) {
            margin: 0;
            color: #666977;
            font-size: 1.4rem;
        }

        .account-overview address,
        .account-overview h3 + p {
            margin-block-start: 1rem;
            overflow-wrap: anywhere;
        }

        .account-overview-action {
            min-height: 4.4rem;
            padding: .6rem 1.1rem;
            border: 1px solid #dcb4d6;
            border-radius: .6rem;
            background: #fff;
            color: #881776;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .account-overview-action:hover,
        .account-overview-action:focus-visible {
            border-color: #ae1c9a;
            background: #fbf0f8;
        }

        .account-overview-action:focus-visible {
            outline: 2px solid #ae1c9a;
            outline-offset: 2px;
        }

        .account-overview-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.2rem;
            margin: 0;
        }

        .account-overview-stats dt {
            color: #666977;
            font-size: 1.4rem;
            font-weight: 500;
        }

        .account-overview-stats dd {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .account-overview-details {
            display: grid;
            align-items: start;
            gap: 1.2rem;
        }

        .account-overview-card-heading,
        .account-overview-order,
        .account-overview-order-summary {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .8rem;
        }

        .account-overview-card-heading,
        .account-overview-order {
            justify-content: space-between;
        }

        .account-overview-orders {
            margin-block-start: 1rem;
        }

        .account-overview-order {
            padding-block: 1rem;
            border-block-start: 1px solid #e5e7eb;
        }

        .account-overview-order:last-child {
            padding-block-end: 0;
        }

        .account-overview-order time {
            display: block;
        }

        .account-overview-status {
            padding: .3rem .8rem;
            border-radius: 1rem;
            background: #fff2d9;
            color: #785100;
            font-size: 1.2rem;
        }

        .account-overview-status:is([data-status="completed"], [data-status="delivered"]) {
            background: #eaf8ef;
            color: #23623a;
        }

        .account-overview-status[data-status="cancelled"] {
            background: #fceced;
            color: #982731;
        }

        .account-orders,
        .account-order-list,
        .account-order-row {
            display: grid;
            gap: 1.2rem;
        }

        .account-orders {
            font-size: 1.5rem;
        }

        .account-orders h2 {
            margin: 0;
            font-size: 2.4rem;
        }

        .account-order-row {
            width: 100%;
            padding: 1.4rem;
            border: 1px solid #e5e7eb;
            border-radius: .8rem;
            background: #fff;
            color: #232532;
            text-align: start;
        }

        .account-order-row:hover,
        .account-order-row[aria-expanded="true"] {
            border-color: #ae1c9a;
        }

        .account-order-row:focus-visible {
            outline: 2px solid #ae1c9a;
            outline-offset: 2px;
        }

        .account-order-heading {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: .6rem;
        }

        .account-order-totals {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr));
            gap: .8rem;
            padding-block-start: 1rem;
            border-block-start: 1px solid #e5e7eb;
        }

        .account-order-label {
            display: block;
            color: #666977;
            font-size: 1.3rem;
        }

        .account-order-details {
            color: #881776;
            font-weight: 600;
        }

        .account-orders .pagination {
            flex-wrap: wrap;
        }

        .account-orders .page-link {
            padding: .7rem 1rem;
            font-size: 1.4rem;
        }

        .account-password {
            padding: clamp(1.6rem, 3vw, 2.4rem);
            border: 1px solid #e5e7eb;
            border-radius: .8rem;
            background: #fff;
            color: #232532;
            text-align: start;
        }

        .account-password-header {
            margin-block-end: 2rem;
            padding-block-end: 1.6rem;
            border-block-end: 1px solid #e5e7eb;
        }

        .account-password-header h2 {
            margin: 0 0 .6rem;
            font-size: 2.4rem;
        }

        .account-password-header p {
            max-inline-size: 60ch;
            margin: 0;
            color: #666977;
            font-size: 1.5rem;
        }

        .account-password-form {
            display: grid;
            gap: 1.6rem;
            max-inline-size: 44rem;
            font-size: 1.5rem;
        }

        .account-password-form .form-label {
            margin-block-end: .6rem;
            font-weight: 600;
        }

        .account-password-form .form-control {
            min-height: 4.6rem;
            padding: .8rem 1.2rem;
            border: 1px solid #aaaab4;
            border-radius: .6rem;
            font-size: 1.6rem;
        }

        .account-password-form .form-control:focus {
            border-color: #ae1c9a;
            box-shadow: 0 0 0 .2rem #ecd9e9;
        }

        .account-password-form .form-control[aria-invalid="true"] {
            border-color: var(--bs-form-invalid-border-color);
        }

        .account-password-error {
            margin: .5rem 0 0;
            color: var(--bs-form-invalid-color);
            font-size: 1.4rem;
        }

        .account-password-save {
            justify-self: start;
            min-height: 4.4rem;
            padding: .8rem 1.6rem;
            border: 0;
            border-radius: .6rem;
            background: #ae1c9a;
            color: #fff;
            font-weight: 600;
        }

        .account-password-save:disabled {
            background: #fbf0f8;
            color: #79546f;
            cursor: not-allowed;
        }

        @media (max-width: 767.98px) {
            .account-dashboard {
                padding-block: 3rem 11rem;
            }

            .account-dashboard-menu {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .account-overview-stats {
                grid-template-columns: 1fr;
            }

            .account-overview-stats .account-overview-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: .8rem;
                padding-block: .9rem;
            }
        }

        @media (min-width: 1200px) {
            .account-overview-details {
                grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    @livewire('website.dashboard.user-dashboard')
@endsection
