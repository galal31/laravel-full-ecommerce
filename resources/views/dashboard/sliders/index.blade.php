@extends('layouts.dashboard.master')

@section('title', __('sliders.manage_sliders'))

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.dataTables.min.css">
@endsection

@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="card border-grey border-lighten-3 m-0">
                    <div class="card-header border-0 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">{{ __('sliders.sliders_list') }}</h4>

                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#createSliderModal">
                            <i class="ft-plus"></i> {{ __('sliders.add_new_slider') }}
                        </button>
                    </div>

                    <div class="card-content">
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2 text-center" id="YajraTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('sliders.image') }}</th>
                                            <th>{{ __('sliders.note') }}</th>
                                            <th>{{ __('sliders.created_at') }}</th>
                                            <th>{{ __('sliders.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createSliderModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <form id="addSliderForm" action="{{ route('dashboard.sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('sliders.add_new_slider') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 form-group">
                                <label>{{ __('sliders.note_ar') }}</label>
                                <input type="text" name="note[ar]" class="form-control" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>{{ __('sliders.note_en') }}</label>
                                <input type="text" name="note[en]" class="form-control" required>
                            </div>

                            <div class="col-md-12 form-group">
                                <label>{{ __('sliders.image') }}</label>
                                <input type="file" name="file_name" class="form-control" accept="image/*" required>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ __('messages.cancel') }}
                        </button>

                        <button type="submit" class="btn btn-primary">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function () {
            let language = "{{ app()->getLocale() }}";

            let table = $('#YajraTable').DataTable({
                processing: true,
                serverSide: true,

                ajax: "{{ route('dashboard.sliders.index') }}",

                columns: [
                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'image',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'note'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        orderable: false
                    }
                ],

                layout: {
                    topStart: {
                        buttons: ['colvis', 'excel']
                    }
                },

                language: language === 'ar'
                    ? { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/ar.json' }
                    : { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/en-GB.json' }
            });

            $('#addSliderForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function (response) {
                        $('#createSliderModal').modal('hide');
                        form[0].reset();

                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: response.message ?? "{{ __('sliders.added_successfully') }}",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },

                    error: function (xhr) {
                        let message = "{{ __('sliders.error_occurred') }}";

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            });

            $(document).on('click', '.delete-slider-btn', function () {
                let deleteUrl = $(this).data('delete-url');

                Swal.fire({
                    title: "{{ __('sliders.are_you_sure') }}",
                    text: "{{ __('sliders.delete_warning') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('messages.yes_delete') }}",
                    cancelButtonText: "{{ __('messages.cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },

                            success: function (response) {
                                table.ajax.reload(null, false);

                                Swal.fire({
                                    icon: 'success',
                                    title: response.message ?? "{{ __('sliders.deleted_successfully') }}",
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            },

                            error: function (xhr) {
                                let message = "{{ __('sliders.error_occurred') }}";

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: message,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection