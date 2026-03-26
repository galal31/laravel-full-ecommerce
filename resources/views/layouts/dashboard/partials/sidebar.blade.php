<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-border" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            {{-- الأدوار (Roles) --}}
            @can('roles')
            <li class="nav-item">
                <a href="#"><i class="la la-user-secret"></i><span class="menu-title">{{ __('sidebar.roles') }}</span>
                    @isset($dashboard_roles_count)
                    <span class="badge badge-info badge-pill float-right mr-2">{{ $dashboard_roles_count }}</span>
                    @endisset
                </a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.roles.index') }}">{{ __('sidebar.manage_roles') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- المشرفين (Admins) --}}
            @can('admins')
            <li class="nav-item">
                <a href="#"><i class="la la-users"></i><span class="menu-title">{{ __('sidebar.admins') }}</span>
                    @isset($dashboard_admins_count)
                    <span class="badge badge-info badge-pill float-right mr-2">{{ $dashboard_admins_count }}</span>
                    @endisset
                </a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.admins.index') }}">{{ __('sidebar.manage_admins') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- الجغرافيا (Geography) --}}
            @can('world')
            <li class="nav-item">
                <a href="#"><i class="la la-globe"></i><span class="menu-title">{{ __('sidebar.geography') }}</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.countries.index') }}">{{ __('sidebar.manage_countries') }}</a></li>
                    <li><a class="menu-item" href="{{ route('dashboard.governorates.index') }}">{{ __('sidebar.manage_governorates') }}</a></li>
                    <li><a class="menu-item" href="{{ route('dashboard.cities.index') }}">{{ __('sidebar.manage_cities') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- الأقسام (Categories) --}}
            @can('categories')
            <li class="nav-item">
                <a href="#"><i class="la la-tags"></i><span class="menu-title">{{ __('sidebar.categories') }}</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.categories.index') }}">{{ __('sidebar.manage_categories') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- الماركات التجارية (Brands) --}}
            @can('brands') {{-- تأكد من إضافة هذه الصلاحية في قاعدة البيانات إذا كنت تستخدمها --}}
            <li class="nav-item">
                <a href="#"><i class="la la-copyright"></i><span class="menu-title">{{ __('sidebar.brands') }}</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.brands.index') }}">{{ __('sidebar.manage_brands') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- الكوبونات (Coupons) --}}
            @can('coupons')
            <li class="nav-item">
                <a href="#"><i class="la la-ticket"></i><span class="menu-title">{{ __('sidebar.coupons') }}</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.coupons.index') }}">{{ __('sidebar.manage_coupons') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- الأسئلة الشائعة (FAQs) --}}
            @can('faqs')
            <li class="nav-item">
                <a href="#"><i class="la la-question-circle"></i><span class="menu-title">{{ __('sidebar.faqs') }}</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.faqs.index') }}">{{ __('sidebar.manage_faqs') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- الإعدادات (Settings) --}}
            @can('settings') {{-- تأكد من إضافة هذه الصلاحية --}}
            <li class="nav-item">
                <a href="#"><i class="la la-cog"></i><span class="menu-title">{{ __('sidebar.settings') }}</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.settings.index') }}">{{ __('sidebar.manage_settings') }}</a></li>
                </ul>
            </li>
            @endcan

        </ul>
    </div>
</div>