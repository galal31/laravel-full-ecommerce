<div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
    {{-- زر التعديل --}}
    <button type="button" class="btn btn-primary btn-sm editBtn" 
        data-question-ar="{{ $faq->getTranslation('question', 'ar') }}"
        data-question-en="{{ $faq->getTranslation('question', 'en') }}"
        data-answer-ar="{{ $faq->getTranslation('answer', 'ar') }}"
        data-answer-en="{{ $faq->getTranslation('answer', 'en') }}"
        data-update-url="{{ route('dashboard.faqs.update', $faq->id) }}"
        data-toggle="modal" data-target="#editFaqModal">
        <i class="fa fa-edit"></i> تعديل
    </button>

    {{-- زر الحذف المتوافق مع كود master.blade.php --}}
    <button type="button" class="btn btn-danger btn-sm delete_confirm"
        data-url="{{ route('dashboard.faqs.destroy', $faq->id) }}"
        data-title="{{ __('messages.delete_title') }}" 
        data-text="{{ __('messages.delete_text') }}"
        data-confirm="{{ __('messages.confirm_delete') }}" 
        data-cancel="{{ __('messages.cancel') }}">
        <i class="fa fa-trash"></i> حذف
    </button>
</div>