<div class="btn-group" role="group" aria-label="Product Actions">
    <a href="{{ route('dashboard.products.show', $row->id) }}" class="btn btn-sm btn-outline-info" title="{{ __('datatables.show') }}">
        <i class="la la-eye"></i>
    </a>

    <a href="{{ route('dashboard.products.edit', $row->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('datatables.edit') }}">
        <i class="la la-edit"></i>
    </a>

    <button type="button" class="btn btn-sm btn-outline-warning toggle-status-btn" data-url="{{ route('dashboard.products.toggleStatus', $row->id) }}" title="{{ __('datatables.change_status') }}">
        <i class="la la-exchange"></i>
    </button>

<button type="button" class="btn btn-sm btn-outline-danger delete_confirm" 
    data-url="{{ route('dashboard.products.destroy', $row->id) }}" 
    data-title="هل أنت متأكد من الحذف؟"
    data-confirm="نعم، احذف"
    data-cancel="إلغاء">
    <i class="la la-trash"></i>
</button>
</div>