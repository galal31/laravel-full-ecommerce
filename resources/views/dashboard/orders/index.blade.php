@extends('layouts.dashboard.master')
@section('title', __('orders.title'))

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap4.min.css">
    <style>
        #orders-table th { white-space: nowrap; padding: .75rem 1.5rem; }
        #orders-table td { vertical-align: middle; padding: .75rem; }
        #orders-table .order-review { min-width: 180px; white-space: normal; }
    </style>
@endsection

@section('content')
    <div class="content-body" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <section class="flexbox-container">
            <div class="col-12">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card border-success">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span>{{ __('orders.completed_count') }}</span>
                                <strong class="h3 mb-0 text-success" id="completed-orders-count">{{ $orderCounts['completed'] }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card border-warning">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <span>{{ __('orders.review_count') }}</span>
                                <strong class="h3 mb-0 text-warning" id="review-orders-count">{{ $orderCounts['needs_review'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-grey border-lighten-3 m-0">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title">{{ __('orders.title') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="needs-review-filter">
                                <label class="form-check-label" for="needs-review-filter">{{ __('orders.only_needs_review') }}</label>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center" id="orders-table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('orders.number') }}</th>
                                            <th>{{ __('orders.customer') }}</th>
                                            <th>{{ __('orders.phone') }}</th>
                                            <th>{{ __('orders.date') }}</th>
                                            <th>{{ __('orders.total') }}</th>
                                            <th>{{ __('orders.status') }}</th>
                                            <th>{{ __('orders.payment') }}</th>
                                            <th>{{ __('orders.review') }}</th>
                                            <th>{{ __('orders.details') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
    <script>
        $(function () {
            var ordersTable = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: @json(route('dashboard.orders.index')),
                    data: function (data) {
                        data.needs_review = $('#needs-review-filter').is(':checked') ? 1 : 0;
                    }
                },
                order: [[3, 'desc'], [0, 'desc']],
                columns: [
                    { data: 'id' },
                    { data: 'user_name' },
                    { data: 'user_phone' },
                    { data: 'created_at', searchable: false },
                    { data: 'total_price', searchable: false },
                    { data: 'status', searchable: false },
                    { data: 'payment_status', searchable: false, orderable: false },
                    { data: 'review', searchable: false, orderable: false },
                    { data: 'actions', searchable: false, orderable: false }
                ],
                language: {
                    url: @json('https://cdn.datatables.net/plug-ins/2.3.7/i18n/'.(app()->getLocale() === 'ar' ? 'ar' : 'en-GB').'.json'),
                    emptyTable: @json(__('orders.empty')),
                    zeroRecords: @json(__('orders.no_results'))
                }
            });

            $('#needs-review-filter').on('change', function () {
                ordersTable.ajax.reload();
            });
        });
    </script>
@endsection
