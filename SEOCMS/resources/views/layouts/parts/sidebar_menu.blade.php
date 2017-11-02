<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>{{ __('adminpanel/adminpanel.common.role') }}</h3>
        <ul class="nav side-menu">
            <li>
                <a href="{{ route('page.index') }}">
                    <i class="fa fa-file-o"></i>{{ __('adminpanel/adminpanel.page.name') }}
                </a>
            </li>
            <li>
                <a href="{{ route('template.index') }}">
                    <i class="fa fa-th"></i>{{ __('adminpanel/adminpanel.template.name') }}
                </a>
            </li>
            <li>
                <a href="{{ route('domain.index') }}">
                    <i class="fa fa-list-alt "></i>{{ __('adminpanel/adminpanel.domain.name') }}
                </a>
            </li>
            <li>
                <a href="{{ route('redirect.index') }}">
                    <i class="fa fa-exchange"></i>{{ __('adminpanel/adminpanel.redirect.name') }}
                </a>
            </li>
            <li>
                <a href="{{ route('critical-css.index') }}">
                    <i class="fa fa-css3"></i>{{ __('adminpanel/adminpanel.critical.name') }}
                </a>
            </li>
        </ul>
    </div>
</div>
