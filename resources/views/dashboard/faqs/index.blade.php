@extends('layouts.dashboard.master')

@section('title', __('faqs.manage_faqs'))

@section('css')
    <style>
        .faq-table {
            table-layout: fixed;
            width: 100%;
        }

        .faq-table .faq-number {
            width: 55px;
        }

        .faq-table .faq-actions {
            width: 190px;
        }

        .faq-table-text {
            display: -webkit-box;
            line-height: 1.7;
            overflow: hidden;
            overflow-wrap: anywhere;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }

        @media (max-width: 767.98px) {
            .faq-table {
                min-width: 760px;
            }
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
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center faq-table">
                                    <thead>
                                        <tr>
                                            <th class="faq-number">#</th>
                                            <th>{{ __('faqs.question') }}</th>
                                            <th>{{ __('faqs.answer') }}</th>
                                            <th class="faq-actions">{{ __('faqs.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($faqs as $faq)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="faq-table-text" title="{{ $faq->getTranslation('question', app()->getLocale()) }}">
                                                        {{ $faq->getTranslation('question', app()->getLocale()) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="faq-table-text" title="{{ $faq->getTranslation('answer', app()->getLocale()) }}">
                                                        {{ $faq->getTranslation('answer', app()->getLocale()) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @include('dashboard.faqs._actions', ['faq' => $faq])
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-muted py-3">{{ __('faqs.no_faqs') }}</td>
                                            </tr>
                                        @endforelse
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="create_question_ar">{{ __('faqs.question_ar') }} <span class="text-danger">*</span></label>
                                <input type="text" id="create_question_ar" name="question[ar]" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="create_question_en">{{ __('faqs.question_en') }} <span class="text-danger">*</span></label>
                                <input type="text" id="create_question_en" name="question[en]" class="form-control" dir="ltr" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="create_answer_ar">{{ __('faqs.answer_ar') }} <span class="text-danger">*</span></label>
                                <textarea id="create_answer_ar" name="answer[ar]" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="create_answer_en">{{ __('faqs.answer_en') }} <span class="text-danger">*</span></label>
                                <textarea id="create_answer_en" name="answer[en]" class="form-control" rows="4" dir="ltr" required></textarea>
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="edit_question_ar">{{ __('faqs.question_ar') }} <span class="text-danger">*</span></label>
                                <input type="text" id="edit_question_ar" name="question[ar]" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="edit_question_en">{{ __('faqs.question_en') }} <span class="text-danger">*</span></label>
                                <input type="text" id="edit_question_en" name="question[en]" class="form-control" dir="ltr" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="edit_answer_ar">{{ __('faqs.answer_ar') }} <span class="text-danger">*</span></label>
                                <textarea id="edit_answer_ar" name="answer[ar]" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="edit_answer_en">{{ __('faqs.answer_en') }} <span class="text-danger">*</span></label>
                                <textarea id="edit_answer_en" name="answer[en]" class="form-control" rows="4" dir="ltr" required></textarea>
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
@endsection

@section('scripts')
    <script>
        $(function() {
            function submitAjaxForm(formElement, modalId) {
                const form = $(formElement);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: new FormData(formElement),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(modalId).modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1200
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = "{{ __('faqs.error_occurred') }}";

                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            errorMessage = '<ul class="text-left text-danger mb-0" style="list-style:none; padding:0;">';

                            $.each(xhr.responseJSON.errors, function(key, messages) {
                                errorMessage += '<li>' + messages[0] + '</li>';
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

            $('#addFaqForm').on('submit', function(event) {
                event.preventDefault();
                submitAjaxForm(this, '#createFaqModal');
            });

            $(document).on('click', '.editBtn', function() {
                const button = $(this);

                $('#editFaqForm').attr('action', button.attr('data-update-url'));
                $('#edit_question_ar').val(button.attr('data-question-ar'));
                $('#edit_question_en').val(button.attr('data-question-en'));
                $('#edit_answer_ar').val(button.attr('data-answer-ar'));
                $('#edit_answer_en').val(button.attr('data-answer-en'));
            });

            $('#editFaqForm').on('submit', function(event) {
                event.preventDefault();
                submitAjaxForm(this, '#editFaqModal');
            });
        });
    </script>
@endsection
