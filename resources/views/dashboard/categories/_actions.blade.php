<div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
    <button type="button" class="btn btn-primary btn-sm editBtn" 
        data-id="{{ $category->id }}"
        data-name-ar="{{ $category->getTranslation('name', 'ar') }}"
        data-name-en="{{ $category->getTranslation('name', 'en') }}"
        data-slug="{{ $category->slug }}"
        data-parent-id="{{ $category->parent_id }}"
        data-update-url="{{ route('dashboard.categories.update', $category->id) }}"
        data-toggle="modal" data-target="#editCategoryModal">
        <i class="fa fa-edit"></i> {{ __('categories.edit') }}
    </button>

    <button type="button" class="btn btn-danger btn-sm delete_confirm"
        data-url="{{ route('dashboard.categories.destroy', $category->id) }}"
        data-title="{{ __('messages.delete_title') }}" data-text="{{ __('messages.delete_text') }}"
        data-confirm="{{ __('messages.confirm_delete') }}" data-cancel="{{ __('messages.cancel') }}">
        <i class="fa fa-trash"></i> {{ __('categories.delete') }}
    </button>

    <button type="button" class="btn btn-warning btn-sm toggle-status-btn"
        data-url="{{ route('dashboard.categories.toggleStatus', $category->id) }}">
        <i class="fa fa-refresh"></i> {{ __('categories.toggle_status') }}
    </button>
</div>