@extends('layouts.dashboard.master')
@section('title', __('categories.manage_categories'))
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.dataTables.min.css">
@endsection

@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="card border-grey border-lighten-3 m-0">
                    <div
                        class="card-header border-0 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">{{ __('categories.categories_list') }}</h4>
                        <button type="button" class="btn btn-outline-info" data-toggle="modal"
                            data-target="#createCategoryModal">
                            <i class="ft-plus"></i> {{ __('categories.add_new_category') }}
                        </button>
                    </div>

                    <div class="card-content">
                        <div class="card-body pb-0">
                            @include('layouts.dashboard.messages')
                        </div>

                        <div class="card-body pt-0">
                            <div id="ajax-alerts"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2 text-center" id="YajraTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('categories.name') }}</th>
                                            <th>{{ __('categories.slug') }}</th>
                                            {{-- <th>{{ __('categories.parent_category') }}</th> --}}
                                            <th>{{ __('categories.status') }}</th>
                                            <th>{{ __('categories.parent_category') }}</th>
                                            <th>{{ __('categories.actions') }}</th>
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

    <form id="delete-form-global" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addCategoryForm" action="{{ route('dashboard.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('categories.add_new_category') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('categories.name_ar') }}</label>
                                <input type="text" name="name[ar]" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('categories.name_en') }}</label>
                                <input type="text" name="name[en]" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('categories.slug') }}</label>
                                <input type="text" name="slug" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('categories.parent_category') }}</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">{{ __('categories.main_category') }}</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->getTranslation('name', app()->getLocale()) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="saveCategoryBtn">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('categories.edit_category') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('categories.name_ar') }}</label>
                                <input type="text" name="name[ar]" id="edit_name_ar" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('categories.name_en') }}</label>
                                <input type="text" name="name[en]" id="edit_name_en" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('categories.slug') }}</label>
                                <input type="text" name="slug" id="edit_slug" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('categories.parent_category') }}</label>
                                <select name="parent_id" id="edit_parent_id" class="form-control">
                                    <option value="">{{ __('categories.main_category') }}</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->getTranslation('name', app()->getLocale()) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="updateCategoryBtn">{{ __('messages.update') }}</button>
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
    {{-- الازرار --}}
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            var language = "{{ app()->getLocale() }}";
            $('#YajraTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.categories.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'slug'
                    },
                    // { data: 'parent', name: 'parent' },
                    {
                        data: 'status'
                    },
                    {
                        data:'parent'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                layout: {
                    topStart: {
                        buttons: [
                            'colvis','excel'
                        ]
                    }
                },
                language: language === 'ar' ? {
                    url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/ar.json'
                } : {
                    url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/en-GB.json'
                }
            });
            
        });
    </script>
    <script>
    // بعد تعريف الـ DataTables الخاص بك
    
    // --- 1. إضافة قسم جديد بالـ AJAX ---
    // --- 1. إضافة قسم جديد بالـ AJAX ---
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let formData = new FormData(this);

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#createCategoryModal').modal('hide');
                form[0].reset();
                $('#YajraTable').DataTable().ajax.reload();
                
                // استخدام SweetAlert بدلاً من alert العادي لتوحيد التصميم
                Swal.fire({
                    icon: 'success',
                    title: "{{ __('categories.added_successfully') }}",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: "{{ __('categories.error_occurred') }}",
                    showConfirmButton: false,
                    timer: 1500
                });
                console.log(xhr.responseText);
            }
        });
    });

    // --- 2. جلب بيانات القسم وعرضها في مودال التعديل ---
    $(document).on('click', '.editBtn', function() {
        let btn = $(this);

        let updateUrl = btn.data('update-url');
        let nameAr = btn.data('name-ar');
        let nameEn = btn.data('name-en');
        let slug = btn.data('slug');
        let parentId = btn.data('parent-id');

        $('#editCategoryForm').attr('action', updateUrl);

        $('#edit_name_ar').val(nameAr);
        $('#edit_name_en').val(nameEn);
        $('#edit_slug').val(slug);
        $('#edit_parent_id').val(parentId).trigger('change'); 
    });

    // --- 3. تحديث القسم بالـ AJAX ---
    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let formData = new FormData(this);

        $.ajax({
            url: url,
            method: "POST", // تأكدنا أنها POST للـ FormData
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editCategoryModal').modal('hide');
                form[0].reset();
                $('#YajraTable').DataTable().ajax.reload();
                
                Swal.fire({
                    icon: 'success',
                    title: "{{ __('categories.updated_successfully') }}",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: "{{ __('categories.error_occurred') }}",
                    showConfirmButton: false,
                    timer: 1500
                });
                console.log(xhr.responseText);
            }
        });
    });
</script>
@endsection
