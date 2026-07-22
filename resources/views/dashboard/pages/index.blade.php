@extends('layouts.dashboard.master')

@section('title', __('pages.manage_pages'))

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
                        <h4 class="card-title mb-2 mb-md-0">{{ __('pages.pages_list') }}</h4>
                        <a href="{{ route('dashboard.pages.create') }}" class="btn btn-outline-info">
                            <i class="ft-plus"></i> {{ __('pages.add_new_page') }}
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
                                            <th>{{ __('pages.title') }}</th>
                                            <th>{{ __('pages.slug') }}</th>
                                            <th>{{ __('pages.content') }}</th>
                                            <th>{{ __('pages.created_at') }}</th>
                                            <th>{{ __('datatables.actions') }}</th>
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
                ajax: "{{ route('dashboard.pages.index') }}",
                columns: [
                    { data: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'title' },
                    { data: 'slug' },
                    { data: 'content', orderable: false },
                    { data: 'created_at' },
                    { data: 'actions', searchable: false, orderable: false },
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
        });
    </script>
@endsection
