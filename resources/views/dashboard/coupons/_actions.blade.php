<div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
    {{-- زر التعديل --}}
    <button type="button" class="btn btn-primary btn-sm editBtn" 
        data-code="{{ $coupon->code }}"
        data-discount="{{ $coupon->discount_percentage }}"
        data-start="{{ $coupon->start_date }}"
        data-expire="{{ $coupon->expire_date }}"
        data-limit="{{ $coupon->limit }}"
        data-update-url="{{ route('dashboard.coupons.update', $coupon->id) }}"
        data-toggle="modal" data-target="#editCouponModal">
        <i class="fa fa-edit"></i> {{ __('coupons.edit_coupon') }}
    </button>

    {{-- زر الحذف مع SweetAlert --}}
    <button type="button" class="btn btn-danger btn-sm delete_confirm"
        data-url="{{ route('dashboard.coupons.destroy', $coupon->id) }}"
        data-title="{{ __('messages.delete_title') }}" 
        data-text="{{ __('messages.delete_text') }}"
        data-confirm="{{ __('messages.confirm_delete') }}" 
        data-cancel="{{ __('messages.cancel') }}">
        <i class="fa fa-trash"></i> {{ __('messages.delete') }}
    </button>

    {{-- زر تغيير الحالة --}}
    <button type="button" class="btn btn-warning btn-sm toggle-status-btn"
        data-url="{{ route('dashboard.coupons.toggleStatus', $coupon->id) }}">
        <i class="fa fa-refresh"></i> {{ __('coupons.toggle_status') }}
    </button>
</div>