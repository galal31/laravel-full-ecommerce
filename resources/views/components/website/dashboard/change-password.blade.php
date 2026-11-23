<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

new class extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function resetPassword(): void
    {
        $this->resetValidation('save');

        $validated = $this->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
        ], attributes: [
            'current_password' => __('website.dashboard_password.current'),
            'password' => __('website.dashboard_password.new'),
            'password_confirmation' => __('website.dashboard_password.confirm'),
        ]);
        try {
            $saved = auth('web')->user()->update([
                'password' => Hash::make($validated['password']),
            ]);
        } catch (QueryException $exception) {
            report($exception->getPrevious());
            $saved = false;
        }

        if (! $saved) {
            $this->addError('save', __('website.dashboard_password.save_failed'));

            return;
        }

        $this->reset('current_password', 'password', 'password_confirmation');
        session()->flash('account_password_saved', __('website.dashboard_password.saved'));
    }
};
?>

<section class="account-password" aria-labelledby="account-password-heading">
    <header class="account-password-header">
        <h2 id="account-password-heading">{{ __('website.dashboard_tabs.change-password') }}</h2>
        <p>{{ __('website.dashboard_password.guidance') }}</p>
    </header>

    <form class="account-password-form" method="post" aria-labelledby="account-password-heading" wire:submit="resetPassword" novalidate>
        <div>
            <label for="current_password" class="form-label">{{ __('website.dashboard_password.current') }}</label>
            <input id="current_password" name="current_password" wire:model="current_password" class="form-control"
                type="password" autocomplete="current-password" required
                @error('current_password') aria-invalid="true" aria-describedby="current-password-error" @enderror>
            @error('current_password')
                <p id="current-password-error" class="account-password-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label">{{ __('website.dashboard_password.new') }}</label>
            <input id="password" name="password" wire:model="password" class="form-control"
                type="password" autocomplete="new-password" required
                @error('password') aria-invalid="true" aria-describedby="new-password-error" @enderror>
            @error('password')
                <p id="new-password-error" class="account-password-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">{{ __('website.dashboard_password.confirm') }}</label>
            <input id="password_confirmation" name="password_confirmation" wire:model="password_confirmation" class="form-control"
                type="password" autocomplete="new-password" required
                @error('password_confirmation') aria-invalid="true" aria-describedby="password-confirmation-error" @enderror>
            @error('password_confirmation')
                <p id="password-confirmation-error" class="account-password-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="account-password-save">
            {{ __('website.dashboard_password.save') }}
        </button>

        @error('save')
            <div class="alert alert-danger mb-0" role="alert">{{ $message }}</div>
        @enderror

        @if (session()->has('account_password_saved'))
            <div class="alert alert-success mb-0" role="status">{{ session('account_password_saved') }}</div>
        @endif
    </form>
</section>
