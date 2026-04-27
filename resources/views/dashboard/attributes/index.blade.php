@extends('layouts.dashboard.master')
@section('title', __('attributes.attributes_and_variants'))

@section('content')
<div class="content-body">
    <section class="flexbox-container">
        <div class="col-12">
            <div class="card border-grey border-lighten-3 m-0">
                <div class="card-header border-0 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h4 class="card-title mb-2 mb-md-0">{{ __('attributes.attributes_list') }}</h4>
                    <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#createAttributeModal">
                        <i class="ft-plus"></i> {{ __('attributes.add_new_attribute') }}
                    </button>
                </div>

                <div class="card-content">
                    <div class="card-body pt-0 mt-2">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.attribute_ar') }}</th>
                                        <th>{{ __('attributes.attribute_en') }}</th>
                                        <th>{{ __('attributes.available_values') }}</th>
                                        <th>{{ __('attributes.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attributes as $index => $attribute)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $attribute->getTranslation('name', 'ar') }}</td>
                                            <td>{{ $attribute->getTranslation('name', 'en') }}</td>
                                            <td>
                                                @foreach($attribute->values as $val)
                                                    <span class="badge badge-primary">{{ $val->getTranslation('value', app()->getLocale()) }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning editBtn" 
                                                    data-id="{{ $attribute->id }}"
                                                    data-name-ar="{{ $attribute->getTranslation('name', 'ar') }}"
                                                    data-name-en="{{ $attribute->getTranslation('name', 'en') }}"
                                                    data-values="{{ json_encode($attribute->values) }}"
                                                    data-update-url="{{ route('dashboard.attributes.update', $attribute->id) }}">
                                                    <i class="ft-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger deleteBtn" data-delete-url="{{ route('dashboard.attributes.destroy', $attribute->id) }}">
                                                    <i class="ft-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">{{ __('attributes.no_data_available') }}</td>
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

<div class="modal fade" id="createAttributeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addAttributeForm" action="{{ route('dashboard.attributes.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('attributes.add_attribute_and_values') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row bg-light p-2 mb-2 rounded">
                        <div class="col-md-6 form-group">
                            <label>{{ __('attributes.attribute_name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name[ar]" class="form-control" placeholder="{{ __('attributes.attribute_name_ar_placeholder') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ __('attributes.attribute_name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name[en]" class="form-control" placeholder="{{ __('attributes.attribute_name_en_placeholder') }}" required>
                        </div>
                    </div>

                    <h6 class="text-bold-600">{{ __('attributes.attribute_values') }}</h6>
                    <div id="create-values-wrapper">
                        <div class="row value-row mb-1 align-items-end">
                            <div class="col-md-5">
                                <label>{{ __('attributes.value_ar') }} <span class="text-danger">*</span></label>
                                <input type="text" name="values[0][ar]" class="form-control" placeholder="{{ __('attributes.value_ar_placeholder') }}" required>
                            </div>
                            <div class="col-md-5">
                                <label>{{ __('attributes.value_en') }} <span class="text-danger">*</span></label>
                                <input type="text" name="values[0][en]" class="form-control" placeholder="{{ __('attributes.value_en_placeholder') }}" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success w-100 add-value-btn"><i class="ft-plus"></i> {{ __('attributes.add') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('attributes.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editAttributeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editAttributeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('attributes.edit_attribute') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row bg-light p-2 mb-2 rounded">
                        <div class="col-md-6 form-group">
                            <label>{{ __('attributes.attribute_name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name[ar]" id="edit_name_ar" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ __('attributes.attribute_name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name[en]" id="edit_name_en" class="form-control" required>
                        </div>
                    </div>

                    <h6 class="text-bold-600">{{ __('attributes.attribute_values') }}</h6>
                    <div id="edit-values-wrapper">
                        </div>
                    <div class="row mt-1">
                         <div class="col-md-12 text-right">
                             <button type="button" class="btn btn-success btn-sm add-edit-value-btn"><i class="ft-plus"></i> {{ __('attributes.add_another_value') }}</button>
                         </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('attributes.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        
        let createIndex = 1;
        $(document).on('click', '.add-value-btn', function() {
            let row = `
                <div class="row value-row mb-1 align-items-end">
                    <div class="col-md-5">
                        <input type="text" name="values[${createIndex}][ar]" class="form-control" placeholder="{{ __('attributes.value_ar') }}" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="values[${createIndex}][en]" class="form-control" placeholder="{{ __('attributes.value_en') }}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger w-100 remove-value-btn"><i class="ft-trash"></i> {{ __('attributes.delete') }}</button>
                    </div>
                </div>
            `;
            $('#create-values-wrapper').append(row);
            createIndex++;
        });

        $(document).on('click', '.remove-value-btn', function() {
            $(this).closest('.value-row').remove();
        });

        
        $('#addAttributeForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: "POST",
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#createAttributeModal').modal('hide');
                    Swal.fire({ icon: 'success', title: response.message, showConfirmButton: false, timer: 1500 });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = xhr.responseJSON.message || "{{ __('attributes.error_occurred') }}";
                    if(errors) {
                        errorMsg = Object.values(errors)[0][0]; 
                    }
                    Swal.fire({ icon: 'error', title: errorMsg, showConfirmButton: true });
                }
            });
        });

        
        let editIndex = 999; 
        $(document).on('click', '.editBtn', function() {
            let btn = $(this);
            $('#editAttributeForm').attr('action', btn.data('update-url'));
            $('#edit_name_ar').val(btn.data('name-ar'));
            $('#edit_name_en').val(btn.data('name-en'));
            
            let values = btn.data('values'); 
            let wrapper = $('#edit-values-wrapper');
            wrapper.empty();

            $.each(values, function(index, item) {
                let valObj = typeof item.value === 'string' ? JSON.parse(item.value) : item.value;
                
                let row = `
                    <div class="row value-row mb-1 align-items-end">
                        <div class="col-md-5">
                            <label>{{ __('attributes.value_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" name="values[${index}][ar]" value="${valObj.ar}" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label>{{ __('attributes.value_en') }} <span class="text-danger">*</span></label>
                            <input type="text" name="values[${index}][en]" value="${valObj.en}" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            ${index === 0 ? '' : '<button type="button" class="btn btn-danger w-100 remove-value-btn"><i class="ft-trash"></i></button>'}
                        </div>
                    </div>
                `;
                wrapper.append(row);
            });

            $('#editAttributeModal').modal('show');
        });

        $(document).on('click', '.add-edit-value-btn', function() {
            let row = `
                <div class="row value-row mb-1 align-items-end">
                    <div class="col-md-5">
                        <input type="text" name="values[${editIndex}][ar]" class="form-control" placeholder="{{ __('attributes.value_ar') }}" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="values[${editIndex}][en]" class="form-control" placeholder="{{ __('attributes.value_en') }}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger w-100 remove-value-btn"><i class="ft-trash"></i> {{ __('attributes.delete') }}</button>
                    </div>
                </div>
            `;
            $('#edit-values-wrapper').append(row);
            editIndex++;
        });

        
        $('#editAttributeForm').on('submit', function(e) {
            e.preventDefault();
            
            
            let formData = new FormData(this);
            formData.append('_method', 'PUT'); 

            $.ajax({
                url: $(this).attr('action'),
                method: "POST", 
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#editAttributeModal').modal('hide');
                    Swal.fire({ icon: 'success', title: response.message, showConfirmButton: false, timer: 1500 });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = xhr.responseJSON.message || "{{ __('attributes.error_updating') }}";
                    if(errors) {
                        errorMsg = Object.values(errors)[0][0]; 
                    }
                    Swal.fire({ icon: 'error', title: errorMsg, showConfirmButton: true });
                }
            });
        });

        
        $(document).on('click', '.deleteBtn', function() {
            let url = $(this).data('delete-url');
            Swal.fire({
                title: "{{ __('attributes.are_you_sure') }}",
                text: "{{ __('attributes.cannot_revert') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('attributes.yes_delete') }}",
                cancelButtonText: "{{ __('attributes.cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE', 
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            Swal.fire({ icon: 'success', title: response.message, showConfirmButton: false, timer: 1500 });
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function(xhr) {
                            Swal.fire({ icon: 'error', title: "{{ __('attributes.error_deleting') }}", showConfirmButton: true });
                        }
                    });
                }
            })
        });
    });
</script>
@endsection