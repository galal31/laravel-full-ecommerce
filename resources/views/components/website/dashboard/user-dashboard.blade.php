<?php

use Livewire\Component;

new class extends Component
{
    public string $selectedTab = 'overview';
    public function selectTab(string $tab): void
    {
        $this->selectedTab=$tab;
    }
};
?>

<section class="container account-dashboard">
    <div class="user-profile-section">
        <div class="dashboard-heading">
            <h1 id="user-dashboard-title" class="dashboard-title">{{ __('site_header.user_dashboard') }}</h1>
        </div>

        <div class="row account-dashboard-grid">
            <nav class="col-12 col-md-4 col-lg-3" aria-labelledby="user-dashboard-title">
                <div class="account-dashboard-menu">
                    @foreach (['overview', 'profile', 'orders', 'wishlist', 'change-password'] as $tab)
                        <button type="button" class="account-dashboard-tab" wire:click.prevent="selectTab('{{ $tab }}')">
                            {{ __('website.dashboard_tabs.' . $tab) }}
                        </button>
                    @endforeach
                </div>
            </nav>

            <div class="col-12 col-md-8 col-lg-9">
                <div class="account-dashboard-panel">
                    <livewire:dynamic-component :is="'website.dashboard.'.$selectedTab" :wire:key="$selectedTab" />
                </div>
            </div>
        </div>
    </div>
</section>
