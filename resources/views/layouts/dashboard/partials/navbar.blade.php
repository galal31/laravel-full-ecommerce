<nav
    class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                        href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item">
                    <a class="navbar-brand" href="index.html">
                        <img class="brand-logo" alt="modern admin logo"
                            src="{{ asset('dashboard-assets/app-assets/images/logo/logo.png') }}">
                        <h3 class="brand-text">Modern Admin</h3>
                    </a>
                </li>
                <li class="nav-item d-md-none">
                    <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i
                            class="la la-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                            href="#"><i class="ft-menu"></i></a></li>
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-link-expand" href="#"><i
                                class="ficon ft-maximize"></i></a></li>
                    <li class="dropdown nav-item mega-dropdown"><a class="dropdown-toggle nav-link" href="#"
                            data-toggle="dropdown">Mega</a>
                        <ul class="mega-dropdown-menu dropdown-menu row">
                            <li class="col-md-2">
                                <h6 class="dropdown-menu-header text-uppercase mb-1"><i class="la la-newspaper-o"></i>
                                    News</h6>
                                <div id="mega-menu-carousel-example">
                                    <div>
                                        <img class="rounded img-fluid mb-1"
                                            src="{{ asset('dashboard-assets/app-assets/images/slider/slider-2.png') }}"
                                            alt="First slide"><a class="news-title mb-0" href="#">Poster Frame
                                            PSD</a>
                                        <p class="news-content">
                                            <span class="font-small-2">January 26, 2018</span>
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li class="col-md-3">
                                <h6 class="dropdown-menu-header text-uppercase"><i class="la la-random"></i> Drill down
                                    menu</h6>
                                <ul class="drilldown-menu">
                                    <li class="menu-list">
                                        <ul>
                                            <li>
                                                <a class="dropdown-item" href="layout-2-columns.html"><i
                                                        class="ft-file"></i> Page layouts & Templates</a>
                                            </li>
                                            <li><a href="#"><i class="ft-align-left"></i> Multi level menu</a>
                                                <ul>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="la la-bookmark-o"></i> Second level</a></li>
                                                    <li><a href="#"><i class="la la-lemon-o"></i> Second level
                                                            menu</a>
                                                        <ul>
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="la la-heart-o"></i> Third level</a></li>
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="la la-file-o"></i> Third level</a></li>
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="la la-trash-o"></i> Third level</a></li>
                                                            <li><a class="dropdown-item" href="#"><i
                                                                        class="la la-clock-o"></i> Third level</a></li>
                                                        </ul>
                                                    </li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="la la-hdd-o"></i> Second level, third link</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="la la-floppy-o"></i> Second level, fourth
                                                            link</a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="color-palette-primary.html"><i
                                                        class="ft-camera"></i> Color palette system</a>
                                            </li>
                                            <li><a class="dropdown-item" href="sk-2-columns.html"><i
                                                        class="ft-edit"></i> Page starter kit</a></li>
                                            <li><a class="dropdown-item" href="changelog.html"><i
                                                        class="ft-minimize-2"></i> Change log</a></li>
                                            <li>
                                                <a class="dropdown-item" href="https://pixinvent.ticksy.com/"><i
                                                        class="la la-life-ring"></i> Customer support center</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="col-md-3">
                                <h6 class="dropdown-menu-header text-uppercase"><i class="la la-list-ul"></i> Accordion
                                </h6>
                                <div id="accordionWrap" role="tablist" aria-multiselectable="true">
                                    <div class="card border-0 box-shadow-0 collapse-icon accordion-icon-rotate">
                                        <div class="card-header p-0 pb-2 border-0" id="headingOne" role="tab"><a
                                                data-toggle="collapse" data-parent="#accordionWrap"
                                                href="#accordionOne" aria-expanded="true"
                                                aria-controls="accordionOne">Accordion Item #1</a></div>
                                        <div class="card-collapse collapse show" id="accordionOne" role="tabpanel"
                                            aria-labelledby="headingOne" aria-expanded="true">
                                            <div class="card-content">
                                                <p class="accordion-text text-small-3">Caramels dessert chocolate cake
                                                    pastry jujubes bonbon.
                                                    Jelly wafer jelly beans. Caramels chocolate cake liquorice
                                                    cake wafer jelly beans croissant apple pie.</p>
                                            </div>
                                        </div>
                                        <div class="card-header p-0 pb-2 border-0" id="headingTwo" role="tab"><a
                                                class="collapsed" data-toggle="collapse" data-parent="#accordionWrap"
                                                href="#accordionTwo" aria-expanded="false"
                                                aria-controls="accordionTwo">Accordion Item #2</a></div>
                                        <div class="card-collapse collapse" id="accordionTwo" role="tabpanel"
                                            aria-labelledby="headingTwo" aria-expanded="false">
                                            <div class="card-content">
                                                <p class="accordion-text">Sugar plum bear claw oat cake chocolate jelly
                                                    tiramisu
                                                    dessert pie. Tiramisu macaroon muffin jelly marshmallow
                                                    cake. Pastry oat cake chupa chups.</p>
                                            </div>
                                        </div>
                                        <div class="card-header p-0 pb-2 border-0" id="headingThree" role="tab">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordionWrap"
                                                href="#accordionThree" aria-expanded="false"
                                                aria-controls="accordionThree">Accordion Item #3</a>
                                        </div>
                                        <div class="card-collapse collapse" id="accordionThree" role="tabpanel"
                                            aria-labelledby="headingThree" aria-expanded="false">
                                            <div class="card-content">
                                                <p class="accordion-text">Candy cupcake sugar plum oat cake wafer
                                                    marzipan jujubes
                                                    lollipop macaroon. Cake dragée jujubes donut chocolate
                                                    bar chocolate cake cupcake chocolate topping.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="col-md-4">
                                <h6 class="dropdown-menu-header text-uppercase mb-1"><i class="la la-envelope-o"></i>
                                    Contact Us</h6>
                                <form class="form form-horizontal">
                                    <div class="form-body">
                                        <div class="form-group row">
                                            <label class="col-sm-3 form-control-label" for="inputName1">Name</label>
                                            <div class="col-sm-9">
                                                <div class="position-relative has-icon-left">
                                                    <input class="form-control" type="text" id="inputName1"
                                                        placeholder="John Doe">
                                                    <div class="form-control-position pl-1"><i class="la la-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 form-control-label" for="inputEmail1">Email</label>
                                            <div class="col-sm-9">
                                                <div class="position-relative has-icon-left">
                                                    <input class="form-control" type="email" id="inputEmail1"
                                                        placeholder="john@example.com">
                                                    <div class="form-control-position pl-1"><i
                                                            class="la la-envelope-o"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 form-control-label"
                                                for="inputMessage1">Message</label>
                                            <div class="col-sm-9">
                                                <div class="position-relative has-icon-left">
                                                    <textarea class="form-control" id="inputMessage1" rows="2" placeholder="Simple Textarea"></textarea>
                                                    <div class="form-control-position pl-1"><i
                                                            class="la la-commenting-o"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 mb-1">
                                                <button class="btn btn-info float-right" type="button"><i
                                                        class="la la-paper-plane-o"></i> Send </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item nav-search"><a class="nav-link nav-link-search" href="#"><i
                                class="ficon ft-search"></i></a>
                        <div class="search-input">
                            <input class="input" type="text" placeholder="Explore Modern...">
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="mr-1">Hello,
                                <span class="user-name text-bold-700">John Doe</span>
                            </span>
                            <span class="avatar avatar-online">
                                <img src="{{ asset('dashboard-assets/app-assets/images/portrait/small/avatar-s-19.png') }}"
                                    alt="avatar"><i></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#"><i
                                    class="ft-user"></i> Edit Profile</a>
                            <a class="dropdown-item" href="#"><i class="ft-mail"></i> My Inbox</a>
                            <a class="dropdown-item" href="#"><i class="ft-check-square"></i> Task</a>
                            <a class="dropdown-item" href="#"><i class="ft-message-square"></i> Chats</a>
                            <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i
                                    class="ft-power"></i> Logout</a>
                        </div>
                    </li>
                    @php
                        $supportedLocales = LaravelLocalization::getSupportedLocales();
                        $currentLocale = app()->getLocale();
                    @endphp

                    <li class="dropdown dropdown-language nav-item">
                        <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">

                            <i class="flag-icon flag-icon-{{ $currentLocale == 'en' ? 'gb' : $currentLocale }}"></i>
                            <span class="selected-language">{{ strtoupper($currentLocale) }}</span>

                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdown-flag">
                            @foreach ($supportedLocales as $localeCode => $properties)
                                <a class="dropdown-item" rel="alternate" hreflang="{{ $localeCode }}"
                                    href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">

                                    {{-- علم --}}
                                    @php
                                        // خريطة بسيطة عشان flag-icon بتستخدم كود دولة مش كود لغة
                                        $flagMap = [
                                            'en' => 'gb',
                                            'ar' => 'eg',
                                            'fr' => 'fr',
                                            'de' => 'de',
                                            'zh' => 'cn',
                                        ];
                                        $flag = $flagMap[$localeCode] ?? $localeCode;
                                    @endphp

                                    <i class="flag-icon flag-icon-{{ $flag }}"></i>
                                    {{ $properties['native'] ?? ($properties['name'] ?? strtoupper($localeCode)) }}

                                </a>
                            @endforeach
                        </div>
                    </li>

                    {{-- جرس الإشعارات مع فحص الصلاحية --}}
                    @can('notifications')
                        @php($currentAdmin = auth('admin')->user())

                        <li class="dropdown dropdown-notification nav-item">
                            <a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i
                                    class="ficon ft-bell"></i>
                                <span id="admin-notification-badge" @if ($unreadCount === 0) hidden @endif
                                    class="admin-notification-count badge badge-pill badge-default badge-danger badge-default badge-up badge-glow">{{ $unreadCount }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <h6 class="dropdown-header m-0">
                                        <span class="grey darken-2">{{ app()->getLocale() == 'ar' ? 'الإشعارات' : 'Notifications' }}</span>
                                    </h6>
                                    <span id="admin-notification-summary" @if ($unreadCount === 0) hidden @endif
                                        class="notification-tag badge badge-default badge-danger float-right m-0">
                                        <span class="admin-notification-count">{{ $unreadCount }}</span>
                                        {{ app()->getLocale() == 'ar' ? 'جديد' : 'New' }}
                                    </span>
                                </li>
                                <li id="admin-notification-list" class="scrollable-container media-list w-100">
                                    @forelse ($adminNotifications as $notification)
                                        @include('layouts.dashboard.partials.notification-row', ['notification' => $notification])
                                    @empty
                                        <div id="admin-notification-empty" class="text-center p-3 text-muted font-small-3">
                                            {{ app()->getLocale() == 'ar' ? 'لا توجد إشعارات حتى الآن' : 'No notifications yet' }}
                                        </div>
                                    @endforelse
                                </li>
                            </ul>
                            <template id="paid-notification-template">
                                @include('layouts.dashboard.partials.notification-row', ['notification' => null])
                            </template>
                        </li>
                        @push('scripts')
                            <script>
                                $(function () {
                                    if (!window.Echo) {
                                        return;
                                    }

                                    let unreadCount = @js($unreadCount);
                                    const orderUrl = @js(route('dashboard.orders.show', ['id' => '__ORDER__'], false));
                                    const paidMessage = @js(__('orders.order_paid', ['id' => '__ORDER__']));
                                    const list = $('#admin-notification-list');



                                    window.Echo.private('App.Models.Dashboard.Admin.{{ $currentAdmin->id }}')
                                        .notification(function (notification) {
                                            const rowId = 'admin-notification-' + notification.id;
                                            if (notification.type !== 'paid_order' || $('#' + rowId).length) {
                                                return;
                                            }

                                            const link = orderUrl.replace('__ORDER__', notification.order_id)
                                                + '?notification_id=' + encodeURIComponent(notification.id);
                                            const row = $($('#paid-notification-template').html());
                                            row.attr({ id: rowId, href: link });
                                            row.find('.notification-message').text(paidMessage.replace('__ORDER__', notification.order_id));
                                            row.find('.notification-total').text(Number(notification.total_price).toLocaleString('en', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            }));
                                            row.find('time').attr('datetime', notification.created_at.replace(' ', 'T'));

                                            $('#admin-notification-empty').remove();
                                            list.prepend(row);
                                            list.find('.admin-notification-row').slice(10).remove();

                                            unreadCount++;
                                            $('.admin-notification-count').text(unreadCount);
                                            $('#admin-notification-badge, #admin-notification-summary').prop('hidden', false);
                                        });
                                });
                            </script>
                        @endpush
                    @endcan
                    @can('contact')
                        <li class="dropdown dropdown-notification nav-item">
                            <a class="nav-link nav-link-label" href="#" data-toggle="dropdown"
                                aria-label="{{ app()->isLocale('ar') ? 'الرسائل' : 'Messages' }}">
                                <i class="ficon ft-mail" aria-hidden="true"></i>
                                <span id="admin-contact-badge" @if ($unreadContactCount === 0) hidden @endif
                                    class="badge badge-pill badge-warning badge-up badge-glow">{{ $unreadContactCount }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <h6 class="dropdown-header m-0">
                                        {{ app()->isLocale('ar') ? 'الرسائل' : 'Messages' }}
                                    </h6>
                                </li>
                                <li id="admin-contact-list" class="scrollable-container media-list w-100">
                                    @forelse ($adminContacts as $contact)
                                        @include('layouts.dashboard.partials.contact-row', ['contact' => $contact])
                                    @empty
                                        <div id="admin-contact-empty" class="text-center p-3 text-muted font-small-3">
                                            {{ app()->isLocale('ar') ? 'لا توجد رسائل حتى الآن' : 'No messages yet' }}
                                        </div>
                                    @endforelse
                                </li>
                                <li class="dropdown-menu-footer">
                                    <a class="dropdown-item text-muted text-center" href="{{ route('dashboard.contacts.index') }}">
                                        {{ app()->isLocale('ar') ? 'عرض كل الرسائل' : 'View all messages' }}
                                    </a>
                                </li>
                            </ul>
                            <template id="admin-contact-template">
                                @include('layouts.dashboard.partials.contact-row', ['contact' => null])
                            </template>
                        </li>

                        @push('scripts')
                            <script>
                                $(function () {
                                    if (!window.Echo) {
                                        return;
                                    }

                                    let unreadContactCount = @js($unreadContactCount);
                                    const contactUrl = @js(route('dashboard.contacts.index', ['contact_id' => '__CONTACT__'], false));
                                    const list = $('#admin-contact-list');
                                    const badge = $('#admin-contact-badge');

                                    window.Echo.private('faqs_sent').listen('FaqSentEvent', function (contact) {
                                        const rowId = 'admin-contact-' + contact.id;
                                        if ($('#' + rowId).length) {
                                            return;
                                        }

                                        const row = $($('#admin-contact-template').html());
                                        row.attr({ id: rowId, href: contactUrl.replace('__CONTACT__', encodeURIComponent(contact.id)) });
                                        row.find('.contact-name').text(contact.name);
                                        row.find('.contact-email').text(contact.email);
                                        row.find('.contact-message').text(contact.message);
                                        row.find('.contact-time').text(contact.created_at);

                                        $('#admin-contact-empty').remove();
                                        list.prepend(row);
                                        list.find('.admin-contact-row').slice(10).remove();
                                        badge.text(++unreadContactCount).prop('hidden', false);
                                    });
                                });
                            </script>
                        @endpush
                    @endcan
                </ul>
            </div>
        </div>
    </div>
</nav>
