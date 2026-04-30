<div class="d-flex justify-content-center align-items-center" style="gap: 5px;">

    <button type="button" class="btn btn-danger btn-sm delete_confirm"
        data-url="{{ route('dashboard.users.destroy', $user->id) }}"
        data-title="{{ __('messages.delete_title') }}" data-text="{{ __('messages.delete_text') }}"
        data-confirm="{{ __('messages.confirm_delete') }}" data-cancel="{{ __('messages.cancel') }}">
        <i class="fa fa-trash"></i> {{ __('users.delete') }}
    </button>

    <button type="button" class="btn btn-warning btn-sm toggle-status-btn"
        data-url="{{ route('dashboard.users.toggleStatus', $user->id) }}">
        <i class="fa fa-refresh"></i> {{ __('users.toggle_status') }}
    </button>
</div>