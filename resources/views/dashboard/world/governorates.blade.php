@extends('layouts.dashboard.master')
@section('title', __('governorates.manage_governorates'))

@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="card border-grey border-lighten-3 m-0">

                    <div class="card-content">
                        <div class="card-body pt-0 mt-2">
                            <div id="ajax-alerts"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('governorates.name') }}</th>
                                            <th>{{ __('governorates.country_name') }}</th>
                                            <th>{{ __('governorates.cities_count') }}</th>
                                            <th>{{ __('governorates.users_count') }}</th>
                                            <th>{{ __('governorates.status') }}</th>
                                            <th>{{ __('governorates.shipping_price') }}</th>
                                            <th>{{ __('governorates.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($governorates as $governorate)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $governorate->name }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-secondary">{{ $governorate->country->name ?? '---' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ $governorate->cities_count }} {{ __('governorates.cities') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        {{ $governorate->users_count }} {{ __('governorates.users') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm toggle-status-btn mb-1 mb-md-0 {{ $governorate->status ? 'btn-success' : 'btn-danger' }}"
                                                        data-id="{{ $governorate->id }}"
                                                        data-status="{{ $governorate->status }}">
                                                        {{ $governorate->status ? __('governorates.active') : __('governorates.inactive') }}
                                                    </button>
                                                </td>
                                                <td>
                                                    <div class="price-container" data-id="{{ $governorate->id }}">
                                                        <span class="price-display badge badge-success"
                                                            style="cursor: pointer; font-size: 14px;"
                                                            title="اضغط لتعديل السعر">
                                                            {{ $governorate->shippingGovernorate->price ?? '0.00' }}
                                                        </span>

                                                        <input type="number"
                                                            class="price-input form-control form-control-sm text-center"
                                                            style="display: none; width: 80px; margin: auto;"
                                                            value="{{ $governorate->shippingGovernorate->price ?? '0.00' }}"
                                                            step="0.01" min="0">
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('dashboard.cities.index', $governorate->id) }}"
                                                        class="btn btn-sm btn-outline-primary mb-1 mb-md-0"
                                                        title="{{ __('governorates.view_cities') }}">
                                                        <i class="la la-map-marker"></i>
                                                        {{ __('governorates.cities') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    {{ __('governorates.no_governorates_found') }}</td>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.toggle-status-btn').on('click', function(e) {
                e.preventDefault();

                let btn = $(this);
                let id = btn.attr('data-id');

                let url = "{{ route('dashboard.governorates.toggleStatus', ':id') }}".replace(':id', id);

                let activeText = "{{ __('governorates.active') }}";
                let inactiveText = "{{ __('governorates.inactive') }}";

                btn.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PATCH",
                        type: "governorate"
                    },
                    success: function(response) {
                        $('#ajax-alerts').html('');

                        if (response.success) {
                            if (response.status == 1 || response.status == true) {
                                btn.removeClass('btn-danger').addClass('btn-success');
                                btn.text(activeText);
                            } else {
                                btn.removeClass('btn-success').addClass('btn-danger');
                                btn.text(inactiveText);
                            }

                            let successHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    تم تغيير الحالة بنجاح.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#ajax-alerts').html(successHtml);
                        }
                    },
                    error: function(xhr) {
                        let errorHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                حدث خطأ في الاتصال بالخادم. برجاء المحاولة لاحقاً.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        $('#ajax-alerts').html(errorHtml);
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });
            // إظهار حقل الإدخال عند الضغط على السعر
            $('.price-display').on('click', function() {
                let container = $(this).closest('.price-container');
                $(this).hide();
                container.find('.price-input').show().focus();
            });

            // إرسال الطلب عند الضغط على Enter أو الخروج من الحقل
            $('.price-input').on('blur keypress', function(e) {
                // لو الحدث هو ضغطة زرار، تأكد إنه زرار Enter (الكود 13)
                if (e.type === 'keypress' && e.which !== 13) {
                    return;
                }

                let input = $(this);
                let container = input.closest('.price-container');
                let display = container.find('.price-display');
                let id = container.data('id');
                let newPrice = input.val();
                let oldPrice = display.text().trim();

                // لو السعر ماتغيرش، ارجع اعرض النص واقفل الحقل بدون طلب AJAX
                if (parseFloat(newPrice) === parseFloat(oldPrice)) {
                    input.hide();
                    display.show();
                    return;
                }

                // تجهيز الرابط (تأكد إن الراوت بياخد بارامتر id)
                let url = "{{ route('dashboard.governorates.changePrice', ':id') }}".replace(':id', id);

                input.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST', // أو PUT/PATCH على حسب تعريفك للراوت في ملف web.php
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PATCH",
                        price: newPrice
                    },
                    success: function(response) {
                        $('#ajax-alerts').html('');

                        if (response.success) {
                            // تحديث السعر المعروض
                            display.text(response.price);
                            input.val(response.price);

                            let successHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        تم تحديث سعر الشحن بنجاح.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                            $('#ajax-alerts').html(successHtml);
                        }
                    },
                    error: function(xhr) {
                        let errorHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    حدث خطأ أثناء حفظ السعر! برجاء التأكد من كتابة رقم صحيح.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
                        $('#ajax-alerts').html(errorHtml);

                        // إرجاع السعر القديم في حالة الخطأ
                        input.val(oldPrice);
                    },
                    complete: function() {
                        // إعادة تفعيل الحقل وإخفائه، وإظهار النص تاني
                        input.prop('disabled', false).hide();
                        display.show();
                    }
                });
            });
        });
    </script>
@endsection
