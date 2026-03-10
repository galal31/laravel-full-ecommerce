@extends('layouts.dashboard.master')
@section('title', __('brands.manage_brands'))
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
                        <h4 class="card-title mb-2 mb-md-0">{{ __('brands.brands_list') }}</h4>
                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#createBrandModal">
                            <i class="ft-plus"></i> {{ __('brands.add_new_brand') }}
                        </button>
                    </div>

                    <div class="card-content">
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2 text-center" id="YajraTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('brands.logo') }}</th>
                                            <th>{{ __('brands.name') }}</th>
                                            <th>{{ __('brands.slug') }}</th>
                                            <th>{{ __('brands.status') }}</th>
                                            <th>{{ __('brands.actions') }}</th>
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

    <div class="modal fade" id="createBrandModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addBrandForm" action="{{ route('dashboard.brands.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('brands.add_new_brand') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('brands.name_ar') }}</label>
                                <input type="text" name="name[ar]" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('brands.name_en') }}</label>
                                <input type="text" name="name[en]" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('brands.slug') }}</label>
                                <input type="text" name="slug" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('brands.logo') }}</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
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

    <div class="modal fade" id="editBrandModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editBrandForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('brands.edit_brand') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('brands.name_ar') }}</label>
                                <input type="text" name="name[ar]" id="edit_name_ar" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('brands.name_en') }}</label>
                                <input type="text" name="name[en]" id="edit_name_en" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('brands.slug') }}</label>
                                <input type="text" name="slug" id="edit_slug" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('brands.logo') }} ({{ __('brands.leave_blank_to_keep') }})</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
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
                ajax: "{{ route('dashboard.brands.index') }}",
                columns: [
                    { data: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'logo', searchable: false, orderable: false },
                    { data: 'name' },
                    { data: 'slug' },
                    { data: 'status' },
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

        // AJAX للإضافة
        $('#addBrandForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#createBrandModal').modal('hide');
                    form[0].reset();
                    $('#YajraTable').DataTable().ajax.reload();
                    Swal.fire({ icon: 'success', title: "{{ __('brands.added_successfully') }}", showConfirmButton: false, timer: 1500 });
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: "{{ __('brands.error_occurred') }}", showConfirmButton: false, timer: 1500 });
                }
            });
        });

        // تعبئة المودال للتعديل
        $(document).on('click', '.editBtn', function() {
            let btn = $(this);
            $('#editBrandForm').attr('action', btn.data('update-url'));
            $('#edit_name_ar').val(btn.data('name-ar'));
            $('#edit_name_en').val(btn.data('name-en'));
            $('#edit_slug').val(btn.data('slug'));
        });

        // AJAX للتعديل
        $('#editBrandForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                method: "POST", // Method Spofing in Laravel is handled by _method='PUT' in FormData
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#editBrandModal').modal('hide');
                    form[0].reset();
                    $('#YajraTable').DataTable().ajax.reload();
                    Swal.fire({ icon: 'success', title: "{{ __('brands.updated_successfully') }}", showConfirmButton: false, timer: 1500 });
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: "{{ __('brands.error_occurred') }}", showConfirmButton: false, timer: 1500 });
                }
            });
        });
    </script>
@endsection