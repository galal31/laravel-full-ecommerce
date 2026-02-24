@extends('layouts.dashboard.master')
@section('title', __('roles.manage_roles'))
@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="card border-grey border-lighten-3 m-0">
                    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                        <h4 class="card-title">{{ __('roles.roles_list') }}</h4>
                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#addRoleModal">
                            <i class="ft-plus"></i> {{ __('roles.add_new_role') }}
                        </button>
                    </div>
                    <div class="card-content">
                        <div class="card-body">@include('layouts.dashboard.messages')</div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('roles.role_name_label') }}</th>
                                        <th>{{ __('roles.permissions_count') }}</th>
                                        <th>{{ __('roles.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ count($role->permissions ?? []) }}
                                                    {{ __('roles.permission_word') }}</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-warning edit-btn"
                                                    data-toggle="modal" data-target="#editRoleModal"
                                                    data-id="{{ $role->id }}" data-name="{{ $role->name }}"
                                                    data-permissions='@json($role->permissions)'>

                                                    <i class="ft-edit"></i>
                                                </button>

                                                <form action="{{ route('dashboard.roles.destroy', $role->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('{{ __('roles.confirm_delete') }}')">
                                                        <i class="ft-trash-2"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('roles.add_new_role') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">{{ __('roles.role_name_label') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required
                                placeholder="{{ __('roles.role_name_placeholder') }}">
                        </div>

                        <label class="font-weight-bold">{{ __('roles.choose_permissions') }}</label>
                        <div class="row border border-lighten-2 p-2 m-0 rounded">
                            @foreach (config('permissions') as $key => $label)
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            id="add_perm_{{ $key }}" name="permissions[]"
                                            value="{{ $key }}">
                                        <label class="custom-control-label" style="cursor: pointer;"
                                            for="add_perm_{{ $key }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('roles.cancel') }}</button>
                        <button type="submit" class="btn btn-info">{{ __('roles.save_role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('roles.edit_role') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('roles.role_name_label') }}</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>

                        <label class="font-weight-bold">{{ __('roles.permissions_label') }}</label>
                        <div class="row border border-lighten-2 p-2 m-0 rounded">
                            @foreach (config('permissions') as $key => $label)
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input edit-checkbox"
                                            id="edit_perm_{{ $key }}" name="permissions[]"
                                            value="{{ $key }}">
                                        <label class="custom-control-label" style="cursor: pointer;"
                                            for="edit_perm_{{ $key }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('roles.cancel') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('roles.update_role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                // 1. استخراج البيانات من الزرار اللي ضغطنا عليه
                let id = $(this).data('id');
                let name = $(this).data('name');
                let permissions = $(this).data('permissions'); // دي هترجع كمصفوفة [ "brands", "orders" ]

                // 2. تحديث رابط الـ Form الخاص بالتعديل عشان يروح للراوت الصح
                let url = "{{ route('dashboard.roles.update', ':id') }}";
                url = url.replace(':id', id);
                $('#editForm').attr('action', url);

                // 3. وضع اسم الدور في حقل الاسم
                $('#edit_name').val(name);

                // 4. تصفير كل الـ Checkboxes الأول (عشان لو كان فاتح دور تاني قبلها متدخلش في بعضها)
                $('.edit-checkbox').prop('checked', false);

                // 5. السحر هنا: هنلف على مصفوفة الصلاحيات ونعلم (Check) على اللي موجود منها
                if (permissions) {
                    // لو كانت الداتا راجعة كـ Object (من بيانات قديمة) بنحولها لـ Array
                    if (!Array.isArray(permissions)) {
                        permissions = Object.keys(permissions);
                    }

                    permissions.forEach(function(perm) {
                        $('#edit_perm_' + perm).prop('checked', true);
                    });
                }
            });
        });
    </script>
@endsection
