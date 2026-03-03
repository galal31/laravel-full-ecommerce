@extends('layouts.dashboard.master')
@section('title', __('cities.manage_cities'))

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
                                            <th>{{ __('cities.name') }}</th>
                                            <th>{{ __('cities.governorate_name') }}</th>
                                            <th>{{ __('cities.users_count') }}</th>
                                            <th>{{ __('cities.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cities as $city)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $city->name }}</td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $city->governorate->name ?? '---' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        {{ $city->users_count }} {{ __('cities.users') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" 
                                                            class="btn btn-sm toggle-status-btn mb-1 mb-md-0 {{ $city->status ? 'btn-success' : 'btn-danger' }}"
                                                            data-id="{{ $city->id }}">
                                                        {{ $city->status ? __('cities.active') : __('cities.inactive') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">{{ __('cities.no_cities_found') }}</td>
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
                let id = btn.attr('data-id'); // بنستخدم attr عشان نتجنب مشاكل الكاش
                
                // نفترض إن الراوت بتاعك اسمه cities.toggleStatus
                let url = "{{ route('dashboard.cities.toggleStatus', ':id') }}".replace(':id', id);

                let activeText = "{{ __('cities.active') }}";
                let inactiveText = "{{ __('cities.inactive') }}";

                // نقفل الزرار لحظياً عشان نمنع الـ Double Click
                btn.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST', 
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PATCH",
                        type: "city" // بنبعت إن النوع هنا مدينة
                    },
                    success: function(response) {
                        $('#ajax-alerts').html(''); 
                        
                        // بنعتمد على اللوجيك السليم اللي اتفقنا عليه
                        if (response.success === true) {
                            
                            // نشوف الحالة الجديدة اللي رجعت من الداتابيز إيه
                            if (response.status == 1 || response.status == true) {
                                btn.removeClass('btn-danger').addClass('btn-success');
                                btn.text(activeText);
                            } else {
                                btn.removeClass('btn-success').addClass('btn-danger');
                                btn.text(inactiveText);
                            }

                            let successHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    تم تعديل الحالة بنجاح.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#ajax-alerts').html(successHtml);

                        } else {
                            // ده في حالة لو الكنترولر رجع success: false بس الريكويست 200 OK
                            let errorHtml = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    ${response.message || 'حدث خطأ غير متوقع.'}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#ajax-alerts').html(errorHtml);
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
                        btn.prop('disabled', false); // نفتح الزرار تاني في كل الأحوال
                    }
                });
            });
        });
    </script>
@endsection