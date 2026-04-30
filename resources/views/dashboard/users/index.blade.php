@extends('layouts.dashboard.master')
@section('title', __('users.manage_users'))
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
                        <h4 class="card-title mb-2 mb-md-0">{{ __('users.users_list') }}</h4>
                        {{-- زر الإضافة كما هو، سيقوم بفتح المودال الموجود في الملف المستدعى --}}
                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#createUserModal">
                            <i class="ft-plus"></i> {{ __('users.add_new_user') }}
                        </button>
                    </div>

                    <div class="card-content">
                        <div class="card-body pb-0">
                            @include('layouts.dashboard.messages')
                        </div>

                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mt-2 text-center" id="YajraTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('users.name') }}</th>
                                            <th>{{ __('users.email') }}</th>
                                            <th>{{ __('users.city') }}</th>
                                            <th>{{ __('users.created_at') }}</th>
                                            <th>{{ __('users.status') }}</th>
                                            <th>{{ __('users.actions') }}</th>
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

    {{-- استدعاء ملف المودال الخاص بالإضافة --}}
    @include('dashboard.users._create')

    {{-- Modal Edit User (متروك كما هو حسب طلبك، ويمكنك أيضاً فصله في ملف _edit.blade.php بنفس الطريقة) --}}
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('users.edit_user') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none print-error-msg">
                            <ul class="mb-0"></ul>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('users.name') }}</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('users.email') }}</label>
                                <input type="email" name="email" id="edit_email" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('users.password') }} <small class="text-muted">({{ __('users.leave_blank_if_no_change') }})</small></label>
                                <input type="password" name="password" id="edit_password" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('users.status') }}</label>
                                <select name="status" id="edit_status" class="form-control" required>
                                    <option value="1">{{ __('users.active') }}</option>
                                    <option value="0">{{ __('users.inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="updateUserBtn">{{ __('messages.update') }}</button>
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
                ajax: "{{ route('dashboard.users.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'city', name: 'city.name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'status', name: 'status' }, 
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ],
                layout: {
                    topStart: {
                        buttons: ['colvis', 'excel']
                    }
                },
                language: language === 'ar' ? {
                    url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/ar.json'
                } : {
                    url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/en-GB.json'
                }
            });

            function printErrorMsg(form, errors) {
                let errorHtml = '';
                $.each(errors, function(key, value) {
                    errorHtml += '<li>' + value[0] + '</li>';
                });
                form.find('.print-error-msg').removeClass('d-none');
                form.find('.print-error-msg ul').html(errorHtml);
            }
            
            // إضافة مستخدم
            $('#addUserForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let formData = new FormData(this);

                form.find('.print-error-msg').addClass('d-none').find('ul').empty();

                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#createUserModal').modal('hide');
                        form[0].reset();
                        $('#YajraTable').DataTable().ajax.reload(null, false);
                        
                        Swal.fire({
                            icon: 'success',
                            title: response.message || "{{ __('messages.done') }}",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            printErrorMsg(form, xhr.responseJSON.errors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "{{ __('messages.error_occurred') }}",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    }
                });
            });

            $('.modal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('.print-error-msg').addClass('d-none').find('ul').empty();
            });
        });
    </script>
@endsection