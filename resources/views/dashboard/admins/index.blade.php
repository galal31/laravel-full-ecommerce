@extends('layouts.dashboard.master')
@section('title', __('admins.manage_admins'))

@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="card border-grey border-lighten-3 m-0">
                    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                        <h4 class="card-title">{{ __('admins.admins_list') }}</h4>
                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#createAdminModal">
                            <i class="ft-plus"></i> {{ __('admins.add_new_admin') }}
                        </button>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-0">
                            @include('layouts.dashboard.messages')
                        </div>
                        
                        <div class="card-body pt-0">
                            <div id="ajax-alerts"></div>

                            <table class="table table-striped table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('admins.name') }}</th>
                                        <th>{{ __('admins.email') }}</th>
                                        <th>{{ __('admins.role') }}</th>
                                        <th>{{ __('admins.status') }}</th>
                                        <th>{{ __('admins.created_at') }}</th>
                                        <th>{{ __('admins.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($admins as $admin)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $admin->role->name ?? __('admins.no_role') }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('dashboard.admins.toggleStatus', $admin->id) }}" method="POST" class="d-inline toggle-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm status-btn {{ $admin->status ? 'btn-success' : 'btn-secondary' }}"
                                                            data-status="{{ $admin->status ? '1' : '0' }}">
                                                        {{ $admin->status ? __('admins.active') : __('admins.inactive') }}
                                                    </button>
                                                </form>
                                            </td>
                                            <td>{{ $admin->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-warning edit-btn"
                                                    data-toggle="modal" data-target="#editAdminModal"
                                                    data-id="{{ $admin->id }}" 
                                                    data-name="{{ $admin->name }}"
                                                    data-email="{{ $admin->email }}" 
                                                    data-role="{{ $admin->role_id }}">
                                                    <i class="ft-edit"></i>
                                                </button>

                                                <form action="{{ route('dashboard.admins.destroy', $admin->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('{{ __('admins.confirm_delete') }}')">
                                                        <i class="ft-trash-2"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">{{ __('admins.no_admins_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="createAdminModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admins.add_new_admin') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.admins.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('admins.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('admins.email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('admins.password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('admins.role') }} <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-control" required>
                                <option value="" disabled selected>{{ __('admins.choose_role') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admins.cancel') }}</button>
                        <button type="submit" class="btn btn-info">{{ __('admins.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admins.edit_admin') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editAdminForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('admins.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('admins.email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('admins.password') }} <small class="text-danger">{{ __('admins.password_hint') }}</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ __('admins.role') }} <span class="text-danger">*</span></label>
                            <select name="role_id" id="edit_role" class="form-control" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admins.cancel') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('admins.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            
            // 1. تعبئة بيانات مودال التعديل
            $('.edit-btn').on('click', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let email = $(this).data('email');
                let role = $(this).data('role');

                let updateUrl = "{{ route('dashboard.admins.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                $('#editAdminForm').attr('action', updateUrl);

                $('#edit_name').val(name);
                $('#edit_email').val(email);
                $('#edit_role').val(role);
            });

            // 2. تغيير حالة المسؤول بالـ AJAX
            $('.toggle-form').on('submit', function(e) {
                e.preventDefault(); 

                let form = $(this);
                let url = form.attr('action');
                let btn = form.find('.status-btn');
                
                let currentStatus = btn.attr('data-status'); 
                let activeText = "{{ __('admins.active') }}";
                let inactiveText = "{{ __('admins.inactive') }}";

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // تفريغ الرسائل القديمة
                        $('#ajax-alerts').html('');

                        if (response.success === true) {
                            // عكس حالة الزرار
                            if (currentStatus === '1') {
                                btn.removeClass('btn-success').addClass('btn-secondary');
                                btn.text(inactiveText);
                                btn.attr('data-status', '0');
                            } else {
                                btn.removeClass('btn-secondary').addClass('btn-success');
                                btn.text(activeText);
                                btn.attr('data-status', '1');
                            }

                            // عرض رسالة النجاح
                            let successHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    ${response.message}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#ajax-alerts').html(successHtml);

                        } else {
                            // عرض رسالة الخطأ
                            let errorHtml = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    ${response.message}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#ajax-alerts').html(errorHtml);
                        }
                    },
                    error: function(xhr) {
                        let fatalErrorHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                حدث خطأ في الاتصال بالخادم. برجاء المحاولة لاحقاً.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        $('#ajax-alerts').html(fatalErrorHtml);
                    }
                });
            });

        });
    </script>
@endsection