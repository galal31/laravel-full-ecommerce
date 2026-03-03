@extends('layouts.dashboard.master')
@section('title', __('countries.manage_countries'))

@section('content')
    <div class="content-body">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="card border-grey border-lighten-3 m-0">
                    <div
                        class="card-header border-0 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">{{ __('countries.countries_list') }}</h4>
                        <button type="button" class="btn btn-outline-info">
                            <i class="ft-plus"></i> {{ __('countries.add_new_country') }}
                        </button>
                    </div>

                    <div class="card-content">
                        <div class="card-body pt-0 mt-2">
                            <div id="ajax-alerts"></div> <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('countries.name') }}</th>
                                            <th>{{ __('countries.phone_code') }}</th>
                                            <th>{{ __('countries.governorates_count') }}</th>
                                            <th>{{ __('countries.users_count') }}</th>
                                            <th>{{ __('countries.status') }}</th>
                                            <th>{{ __('countries.actions') }}</th> </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($countries as $country)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $country->name }}</td>
                                                <td>
                                                    <span class="badge badge-secondary"
                                                        dir="ltr">{{ $country->phone_code }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ $country->governorates_count }}
                                                        {{ __('countries.governorates') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        {{ $country->users_count }} {{ __('countries.users') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm toggle-status-btn mb-1 mb-md-0 {{ $country->status ? 'btn-success' : 'btn-danger' }}"
                                                        data-id="{{ $country->id }}"
                                                        data-status="{{ $country->status }}">
                                                        {{ $country->status ? __('countries.active') : __('countries.inactive') }}
                                                    </button>
                                                </td>
                                                <td>
                                                    <a href="{{ route('dashboard.governorates.index', $country->id) }}" 
                                                       class="btn btn-sm btn-outline-primary mb-1 mb-md-0" 
                                                       title="{{ __('countries.view_governorates') }}">
                                                        <i class="la la-map-marker"></i>
                                                        {{ __('countries.governorates') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    {{ __('countries.no_countries_found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-2">
                                {{-- {{ $countries->links() }} --}}
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
                // بنستخدم attr هنا عشان نعالج مشكلة الكاشنج لو ضغط مرتين
                let id = btn.attr('data-id'); 

                // تجهيز الرابط (تأكد من اسم الراوت عندك)
                let url = "{{ route('dashboard.countries.toggleStatus', ':id') }}";
                url = url.replace(':id', id);

                let activeText = "{{ __('countries.active') }}";
                let inactiveText = "{{ __('countries.inactive') }}";

                btn.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PATCH",
                        type: "country"
                    },
                    success: function(response) {
                        $('#ajax-alerts').html('');

                        // بناءً على اللوجيك الجديد: response.success بيأكد النجاح، و response.status شايل الحالة
                        if (response.success === true) {
                            
                            if (response.status == 1 || response.status == true) {
                                // لو true يبقى خلي الزرار أخضر ونشط
                                btn.removeClass('btn-danger').addClass('btn-success');
                                btn.text(activeText);
                            } else {
                                // لو false يبقى خلي الزرار أحمر وغير نشط
                                btn.removeClass('btn-success').addClass('btn-danger');
                                btn.text(inactiveText);
                            }

                            // وفي الحالتين هنطبع رسالة النجاح
                            let successHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    تم تغيير الحالة بنجاح.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#ajax-alerts').html(successHtml);

                        } else {
                            // لو الكنترولر رجع success: false لسبب ما
                            alert('حدث خطأ أثناء تنفيذ العملية.');
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

        });
    </script>
@endsection