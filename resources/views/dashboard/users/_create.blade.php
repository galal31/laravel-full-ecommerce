{{-- Modal Create User --}}
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addUserForm" action="{{ route('dashboard.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('users.add_new_user') }}</h5>
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
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ __('users.email') }}</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ __('users.password') }}</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ __('users.status') }}</label>
                            <select name="is_active" class="form-control" required>
                                <option value="1">{{ __('users.active') }}</option>
                                <option value="0">{{ __('users.inactive') }}</option>
                            </select>
                        </div>

                        {{-- استدعاء كومبوننت اللايف واير الخاص بالدولة والمحافظة والمدينة --}}
                        <div class="col-md-12">
                            <livewire:general.address-dropdown />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="saveUserBtn">{{ __('messages.save') }}</button>
                </div>
                
            </form>
        </div>
    </div>
</div>