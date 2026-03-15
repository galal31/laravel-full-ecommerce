{{-- AI GENERATED THEME --}}

@extends('layouts.dashboard.master')
@section('title', __('faqs.manage_faqs'))

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.dataTables.min.css">
    <style>
        /* إضافة ستايل بسيط عشان الإجابات الطويلة متكسرش شكل الجدول */
        .faq-text-truncate {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endsection

@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="card border-grey border-lighten-3 m-0">
                    <div class="card-header border-0 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">{{ __('faqs.faqs_list') }}</h4>
                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#createFaqModal">
                            <i class="ft-plus"></i> {{ __('faqs.add_new_faq') }}
                        </button>
                    </div>

                    <div class="card-content">
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2 text-center" id="YajraTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('faqs.question') }}</th>
                                            <th>{{ __('faqs.answer') }}</th>
                                            <th>{{ __('faqs.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="createFaqModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addFaqForm" action="{{ route('dashboard.faqs.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('faqs.add_new_faq') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('faqs.question_ar') }} <span class="text-danger">*</span></label>
                                <input type="text" name="question[ar]" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('faqs.question_en') }} <span class="text-danger">*</span></label>
                                <input type="text" name="question[en]" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>{{ __('faqs.answer_ar') }} <span class="text-danger">*</span></label>
                                <textarea name="answer[ar]" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('faqs.answer_en') }} <span class="text-danger">*</span></label>
                                <textarea name="answer[en]" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editFaqModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editFaqForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('faqs.edit_faq') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('faqs.question_ar') }} <span class="text-danger">*</span></label>
                                <input type="text" name="question[ar]" id="edit_question_ar" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('faqs.question_en') }} <span class="text-danger">*</span></label>
                                <input type="text" name="question[en]" id="edit_question_en" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('faqs.answer_ar') }} <span class="text-danger">*</span></label>
                                <textarea name="answer[ar]" id="edit_answer_ar" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('faqs.answer_en') }} <span class="text-danger">*</span></label>
                                <textarea name="answer[en]" id="edit_answer_en" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <form id="delete-form-global" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            var language = "{{ app()->getLocale() }}";
            $('#YajraTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.faqs.index') }}",
                columns: [
                    { data: 'DT_RowIndex', searchable: false, orderable: false },
                    { 
                        data: 'question', 
                        name: 'question',
                        // كلاس faq-text-truncate عشان لو السؤال طويل ميبوظش عرض الجدول
                        render: function(data, type, row) {
                            return '<div class="faq-text-truncate" title="'+data+'">' + data + '</div>';
                        }
                    },
                    { 
                        data: 'answer', 
                        name: 'answer',
                        render: function(data, type, row) {
                            return '<div class="faq-text-truncate" title="'+data+'">' + data + '</div>';
                        }
                    },
                    { data: 'actions', orderable: false, searchable: false },
                ],
                layout: {
                    topStart: { buttons: ['colvis','excel'] }
                },
                language: language === 'ar' 
                    ? { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/ar.json' } 
                    : { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/en-GB.json' }
            });
        });

        // نفس الدالة المساعدة اللي عملناها قبل كده لتوحيد الـ AJAX
        function submitAjaxForm(formElement, modalId) {
            let form = $(formElement);
            let formData = new FormData(formElement);
            let url = form.attr('action');

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $(modalId).modal('hide');
                    form[0].reset();
                    $('#YajraTable').DataTable().ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    let errorMessage = "{{ __('messages.error_occurred') }}";
                    
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        errorMessage = '<ul class="text-left text-danger" style="list-style:none; padding:0;">';
                        $.each(errors, function(key, value) {
                            errorMessage += '<li>' + value[0] + '</li>'; 
                        });
                        errorMessage += '</ul>';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: "{{ __('messages.error') }}",
                        html: errorMessage,
                        confirmButtonText: "{{ __('messages.ok') }}"
                    });
                }
            });
        }

        // تنفيذ الـ Submit للإضافة
        $('#addFaqForm').on('submit', function(e) {
            e.preventDefault();
            submitAjaxForm(this, '#createFaqModal');
        });

        // تعبئة البيانات في المودال لما ندوس على زر التعديل
        $(document).on('click', '.editBtn', function() {
            let btn = $(this);
            $('#editFaqForm').attr('action', btn.data('update-url'));
            $('#edit_question_ar').val(btn.data('question-ar'));
            $('#edit_question_en').val(btn.data('question-en'));
            $('#edit_answer_ar').val(btn.data('answer-ar'));
            $('#edit_answer_en').val(btn.data('answer-en'));
        });

        // تنفيذ الـ Submit للتعديل
        $('#editFaqForm').on('submit', function(e) {
            e.preventDefault();
            submitAjaxForm(this, '#editFaqModal');
        });
    </script>
@endsection