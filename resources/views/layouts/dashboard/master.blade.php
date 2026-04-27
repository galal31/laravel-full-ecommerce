<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="Modern admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities with bitcoin dashboard.">
    <meta name="keywords"
        content="admin template, modern admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="PIXINVENT">
    <title>@yield('title', 'Modern Admin')</title>
    <link rel="apple-touch-icon" href="{{ asset('dashboard-assets/app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="shortcut icon" type="image/x-icon"
        href="{{ asset('dashboard-assets/app-assets/images/ico/favicon.ico') }}">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    @if (config('app.locale') == 'ar')
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css-rtl/vendors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css-rtl/app.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css-rtl/custom-rtl.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard-assets/app-assets/css-rtl/core/menu/menu-types/vertical-overlay-menu.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard-assets/app-assets/css-rtl/core/colors/palette-gradient.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard-assets/app-assets/css-rtl/pages/timeline.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css-rtl/style.css') }}">
    @else
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css/vendors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css/app.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard-assets/app-assets/css/core/menu/menu-types/vertical-overlay-menu.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard-assets/app-assets/css/core/colors/palette-gradient.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard-assets/app-assets/css/pages/timeline.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard-assets/app-assets/css/style.css') }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.21/dist/sweetalert2.min.css"
        integrity="sha256-VqBagSPahwzjkw8/E0KAY23AmFZuYBxX6f6uVDgY1rg=" crossorigin="anonymous">
    @yield('css')
</head>

<body class="vertical-layout vertical-overlay-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-overlay-menu" data-col="2-columns">

    @include('layouts.dashboard.partials.navbar')
    @include('layouts.dashboard.partials.sidebar')
    <div class="app-content content">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    @include('layouts.dashboard.partials.footer')
    <script src="{{ asset('dashboard-assets/app-assets/vendors/js/vendors.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('dashboard-assets/app-assets/js/core/app-menu.js') }}" type="text/javascript"></script>
    <script src="{{ asset('dashboard-assets/app-assets/js/core/app.js') }}" type="text/javascript"></script>
    <script src="{{ asset('dashboard-assets/app-assets/js/scripts/customizer.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.21/dist/sweetalert2.all.min.js"
        integrity="sha256-DZ0W5YqW5Cigui/h8X//bdI8EShzkDIjIrgLKVOIHCs=" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // دالة الحذف العامة باستخدام AJAX
        // دالة الحذف العامة باستخدام AJAX
        $(document).on('click', '.delete_confirm', function(e) {
            e.preventDefault();

            let url = $(this).data('url');
            let title = $(this).data('title');
            // التعديل هنا: وضعنا علامات تنصيص
            let text = "{{ __('messages.delete_text') }}" ; 
            let confirmText = "{{ __('messages.confirm_delete') }}" ;
            let cancelText = "{{ __('messages.cancel') }}" ;

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // إرسال الطلب عبر AJAX
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE', // تعريف لارافيل إن ده طلب حذف
                            _token: '{{ csrf_token() }}' // تمرير التوكن للحماية
                        },
                        success: function(response) {
                            if (response.success) {
                                // عرض رسالة النجاح
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                
                                // تحديث الـ DataTable لو موجود في الصفحة بدون ريفريش
                                if ($.fn.DataTable.isDataTable('#YajraTable')) {
                                    $('#YajraTable').DataTable().ajax.reload(null, false);
                                } else {
                                    // لو الصفحة مفيهاش DataTable، يعمل ريفريش عادي
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                }
                            } else {
                                // في حالة رجوع success: false من الكنترولر
                                Swal.fire('خطأ', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            // في حالة حدوث خطأ في السيرفر (500)
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'حدث خطأ أثناء تنفيذ العملية.',
                                confirmButtonText: 'حسناً'
                            });
                        }
                    });
                }
            });
        });

            // toggle status
            $(document).on('click', '.toggle-status-btn', function(e) {
                e.preventDefault();

                var url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}' // ضروري لتمرير حماية لارافيل
                    },
                    success: function(response) {
                        if (response.success) {
                            // تحديث الجدول بدون إعادة تحميل الصفحة
                            $('#YajraTable').DataTable().ajax.reload(null, false);

                            // إظهار رسالة نجاح (اختياري: يمكنك استخدام Toastr أو SweetAlert)
                            $('#ajax-alerts').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                        </div>
                    `);
                        }
                    },
                    error: function(xhr) {
                        alert('حدث خطأ أثناء تغيير الحالة.');
                    }
                });
            });
        })
    </script>
    @yield('scripts')
</body>

</html>
