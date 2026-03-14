@extends('layouts.dashboard.master')
@section('title', __('coupons.manage_coupons'))
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
                        <h4 class="card-title mb-2 mb-md-0">{{ __('coupons.coupons_list') }}</h4>
                        <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#createCouponModal">
                            <i class="ft-plus"></i> {{ __('coupons.add_new_coupon') }}
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
                                            <th>{{ __('coupons.code') }}</th>
                                            <th>{{ __('coupons.discount_percentage') }} (%)</th>
                                            <th>{{ __('coupons.start_date') }}</th>
                                            <th>{{ __('coupons.expire_date') }}</th>
                                            <th>{{ __('coupons.limit') }}</th>
                                            <th>{{ __('coupons.times_used') }}</th>
                                            <th>{{ __('coupons.status') }}</th>
                                            <th>{{ __('coupons.actions') }}</th>
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

    {{-- Modal Create --}}
    <div class="modal fade" id="createCouponModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addCouponForm" action="{{ route('dashboard.coupons.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('coupons.add_new_coupon') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- مساحة عرض أخطاء الـ Validation --}}
                        <div class="alert alert-danger d-none print-error-msg">
                            <ul class="mb-0"></ul>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.code') }}</label>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.discount_percentage') }} (%)</label>
                                <input type="number" name="discount_percentage" class="form-control" min="1" max="100" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.start_date') }}</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.expire_date') }}</label>
                                <input type="date" name="expire_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.limit') }}</label>
                                <input type="number" name="limit" class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="saveCouponBtn">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editCouponModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editCouponForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('coupons.edit_coupon') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- مساحة عرض أخطاء الـ Validation --}}
                        <div class="alert alert-danger d-none print-error-msg">
                            <ul class="mb-0"></ul>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.code') }}</label>
                                <input type="text" name="code" id="edit_code" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.discount_percentage') }} (%)</label>
                                <input type="number" name="discount_percentage" id="edit_discount_percentage" class="form-control" min="1" max="100" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.start_date') }}</label>
                                <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.expire_date') }}</label>
                                <input type="date" name="expire_date" id="edit_expire_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('coupons.limit') }}</label>
                                <input type="number" name="limit" id="edit_limit" class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="updateCouponBtn">{{ __('messages.update') }}</button>
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
                ajax: "{{ route('dashboard.coupons.index') }}",
                columns: [
                    { data: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'code' },
                    { data: 'discount_percentage' },
                    { data: 'start_date' },
                    { data: 'expire_date' },
                    { data: 'limit' },
                    { data: 'times_used' },
                    { data: 'status' }, 
                    { data: 'actions', orderable: false, searchable: false },
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

            // دالة مساعدة لعرض الأخطاء
            function printErrorMsg(form, errors) {
                let errorHtml = '';
                $.each(errors, function(key, value) {
                    errorHtml += '<li>' + value[0] + '</li>';
                });
                form.find('.print-error-msg').removeClass('d-none');
                form.find('.print-error-msg ul').html(errorHtml);
            }
            
            // إضافة كوبون
            $('#addCouponForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let formData = new FormData(this);

                // إخفاء الأخطاء القديمة قبل الإرسال الجديد
                form.find('.print-error-msg').addClass('d-none').find('ul').empty();

                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#createCouponModal').modal('hide');
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
                        // لو الخطأ 422 (Validation Error من لارافل)
                        if (xhr.status === 422) {
                            printErrorMsg(form, xhr.responseJSON.errors);
                        } else {
                            // لو خطأ تاني (سيرفر مثلاً)
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

            // جلب الداتا للتعديل
            $(document).on('click', '.editBtn', function() {
                let btn = $(this);
                let form = $('#editCouponForm');
                
                // تصفير أي أخطاء قديمة
                form.find('.print-error-msg').addClass('d-none').find('ul').empty();
                
                form.attr('action', btn.data('update-url'));
                $('#edit_code').val(btn.data('code'));
                $('#edit_discount_percentage').val(btn.data('discount'));
                $('#edit_start_date').val(btn.data('start'));
                $('#edit_expire_date').val(btn.data('expire'));
                $('#edit_limit').val(btn.data('limit'));
            });

            // تحديث الكوبون
            $('#editCouponForm').on('submit', function(e) {
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
                        $('#editCouponModal').modal('hide');
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

            // تنظيف المودال لما يتقفل عشان ميفتحش على أخطاء قديمة
            $('.modal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('.print-error-msg').addClass('d-none').find('ul').empty();
            });
        });
    </script>
@endsection