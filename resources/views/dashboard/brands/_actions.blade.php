<div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
    <button type="button" class="btn btn-primary btn-sm editBtn" 
        data-id="{{ $brand->id }}"
        data-name-ar="{{ $brand->getTranslation('name', 'ar') }}"
        data-name-en="{{ $brand->getTranslation('name', 'en') }}"
        data-slug="{{ $brand->slug }}"
        data-update-url="{{ route('dashboard.brands.update', $brand->id) }}"
        data-toggle="modal" data-target="#editBrandModal">
        <i class="fa fa-edit"></i> {{ __('brands.edit') }}
    </button>

    <button type="button" class="btn btn-danger btn-sm delete_confirm"
        data-url="{{ route('dashboard.brands.destroy', $brand->id) }}"
        data-title="{{ __('messages.delete_title') }}" data-text="{{ __('messages.delete_text') }}"
        data-confirm="{{ __('messages.confirm_delete') }}" data-cancel="{{ __('messages.cancel') }}">
        <i class="fa fa-trash"></i> {{ __('brands.delete') }}
    </button>

    <button type="button" class="btn btn-warning btn-sm toggle-status-btn"
        data-url="{{ route('dashboard.brands.toggleStatus', $brand->id) }}">
        <i class="fa fa-refresh"></i> {{ __('brands.toggle_status') }}
    </button>
</div>