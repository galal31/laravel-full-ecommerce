@extends('layouts.dashboard.master')
@section('title', __('products.manage_products'))
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
                        <h4 class="card-title mb-2 mb-md-0">{{ __('products.products_list') }}</h4>
                        {{-- تم تغيير الزر ليوجه لصفحة الـ Create بدلاً من فتح Modal --}}
                        <a href="{{ route('dashboard.products.create') }}" class="btn btn-outline-info">
                            <i class="ft-plus"></i> {{ __('products.add_new_product') }}
                        </a>
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
                                            <th>{{ __('products.name') }}</th>
                                            <th>{{ __('products.sku') }}</th>
                                            <th>{{ __('products.category') }}</th>
                                            <th>{{ __('products.brand') }}</th>
                                            <th>{{ __('products.price') }}</th>
                                            <th>{{ __('products.status') }}</th>
                                            <th>{{ __('products.actions') }}</th>
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
            var table = $('#YajraTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.products.index') }}",
                columns: [
                    { data: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'name' },
                    { data: 'sku' },
                    { data: 'category', orderable: false },
                    { data: 'brand', orderable: false },
                    { data: 'price' },
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

            // التعامل مع زر الحذف (Delete) باستخدام SweetAlert و AJAX
            // $(document).on('click', '.delete-btn', function() {
            //     let url = $(this).data('url');
                
            //     Swal.fire({
            //         title: "{{ __('messages.are_you_sure') }}",
            //         text: "{{ __('messages.cannot_revert') }}",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: "{{ __('messages.yes_delete') }}",
            //         cancelButtonText: "{{ __('messages.cancel') }}"
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             $.ajax({
            //                 url: url,
            //                 type: 'DELETE',
            //                 data: {
            //                     _token: '{{ csrf_token() }}'
            //                 },
            //                 success: function(response) {
            //                     table.ajax.reload(null, false);
            //                     Swal.fire({
            //                         icon: 'success',
            //                         title: response.message || "{{ __('messages.done') }}",
            //                         showConfirmButton: false,
            //                         timer: 1500
            //                     });
            //                 },
            //                 error: function(xhr) {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: "{{ __('messages.error_occurred') }}",
            //                         showConfirmButton: false,
            //                         timer: 1500
            //                     });
            //                 }
            //             });
            //         }
            //     });
            // });
            $(document).on('click', '.toggle-status-btn', function() {
    let url = $(this).data('url');
    let table = $('#YajraTable').DataTable(); 

    // إرسال الطلب مباشرة بدون أي رسائل تأكيد
    $.ajax({
        url: url,
        type: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // تحديث الجدول في الخلفية فوراً للاحتفاظ بنفس الصفحة
                table.ajax.reload(null, false); 
            }
        },
        error: function(xhr) {
            // (اختياري) تنبيه بسيط جداً في حالة حدوث خطأ في السيرفر فقط عشان المبرمج يعرف
            console.error("حدث خطأ أثناء تغيير الحالة");
        }
    });
});
        });
    </script>
@endsection