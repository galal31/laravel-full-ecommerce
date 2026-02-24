<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-border" data-scroll-to-active="true">
    <div class="main-menu-content">
        @can('roles')
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a href="index.html"><i class="la la-home"></i><span class="menu-title"
                        data-i18n="nav.dash.main">Roles</span><span
                        class="badge badge badge-info badge-pill float-right mr-2">{{ $dashboard_roles }}</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="{{ route('dashboard.roles.index') }}"
                            data-i18n="nav.dash.ecommerce">Mangae roles</a>
                    </li>
                </ul>
            </li>
        </ul>
          
        @endcan
    </div>
</div>
