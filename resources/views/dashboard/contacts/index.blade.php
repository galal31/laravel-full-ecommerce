@extends('layouts.dashboard.master')

@section('title', 'Email Application')

@section('css')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('dashboard-assets/app-assets/fonts/simple-line-icons/style.min.css') }}">
    @if (config('app.locale') == 'ar')
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard-assets/app-assets/css-rtl/pages/email-application.css') }}">
    @else
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard-assets/app-assets/css/pages/email-application.css') }}">
    @endif

    <style>
        /* Fix email template inside the normal dashboard master layout */
        .email-page-fixed {
            width: 100%;
            overflow-x: hidden;
        }

        .email-page-fixed .email-layout-fix {
            display: flex;
            align-items: stretch;
            gap: 1rem;
            width: 100%;
            min-width: 0;
        }

        .email-page-fixed .sidebar-left {
            position: relative !important;
            float: none !important;
            inset: auto !important;
            transform: none !important;
            width: 430px !important;
            max-width: 430px !important;
            flex: 0 0 430px;
            z-index: 1;
        }

        .email-page-fixed .sidebar,
        .email-page-fixed .sidebar-content {
            position: relative !important;
            width: 100% !important;
            height: auto !important;
        }

        .email-page-fixed .email-app-sidebar {
            width: 100% !important;
            min-width: 0;
        }

        .email-page-fixed .email-app-menu {
            flex: 0 0 42%;
            max-width: 42%;
        }

        .email-page-fixed .email-app-list-wraper {
            flex: 1 1 auto;
            max-width: 58%;
            min-width: 0;
        }

        .email-page-fixed .content-right {
            position: relative !important;
            float: none !important;
            margin: 0 !important;
            width: auto !important;
            max-width: none !important;
            flex: 1 1 0;
            min-width: 0;
            display: block !important;
        }

        .email-page-fixed .email-app-details,
        .email-page-fixed .email-app-list,
        .email-page-fixed .email-app-list-wraper,
        .email-page-fixed .email-app-menu {
            height: auto !important;
        }

        .email-page-fixed .card {
            margin-bottom: 0;
        }

        .email-page-fixed .media-body,
        .email-page-fixed .email-app-message,
        .email-page-fixed .email-app-title,
        .email-page-fixed .email-app-text {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        @media (max-width: 1199.98px) {
            .email-page-fixed .email-layout-fix {
                display: block;
            }

            .email-page-fixed .sidebar-left,
            .email-page-fixed .content-right {
                width: 100% !important;
                max-width: 100% !important;
                flex: none !important;
                margin-bottom: 1rem !important;
            }

            .email-page-fixed .email-app-menu {
                display: block !important;
            }
        }

        @media (max-width: 767.98px) {
            .email-page-fixed .email-app-sidebar {
                display: block !important;
            }

            .email-page-fixed .email-app-menu,
            .email-page-fixed .email-app-list-wraper {
                width: 100% !important;
                max-width: 100% !important;
                flex: none !important;
                margin-bottom: 1rem;
            }

            .email-page-fixed .email-app-list .media,
            .email-page-fixed .email-app-sender.media {
                align-items: flex-start;
            }

            .email-page-fixed .email-app-title h3 {
                font-size: 1.2rem;
            }
        }
        /* Make inbox stop growing down and scroll internally */
.email-page-fixed .contact-inbox-card {
    height: calc(100vh - 180px) !important;
    max-height: calc(100vh - 180px) !important;
    min-height: 0;
    overflow: visible;
}

.email-page-fixed .contact-inbox-card .email-app-list {
    height: 100% !important;
    max-height: 100% !important;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.email-page-fixed .contact-inbox-card .chat-fixed-search {
    flex: 0 0 auto;
}

.email-page-fixed .contact-inbox-card #users-list {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
}

.email-page-fixed .contact-inbox-card .card-footer {
    flex: 0 0 auto;
    padding: 0.75rem 1rem;
    overflow: visible;
    background: #fff;
}
.email-page-fixed .contact-inbox-card #users-list {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
}.email-page-fixed .contact-inbox-card .pagination {
    margin-bottom: 0;
    flex-wrap: wrap;
}
    </style>
@endsection

@section('content')
    <div class="content-body email-page-fixed">
        <div class="email-layout-fix">
            @livewire('dashboard.contacts.contact-sidebar')
            @livewire('dashboard.contacts.contact-show')



        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('dashboard-assets/app-assets/js/scripts/pages/email-application.js') }}" type="text/javascript">
    </script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('openReplyModal', () => {
                $('#replyContactModal').modal('show');
            });

            Livewire.on('closeReplyModal', () => {
                $('#replyContactModal').modal('hide');
            });
        });
    </script>
@endsection
