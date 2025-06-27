<div class="bg-dark text-white vh-100 p-3" style="width: 250px;">
    <h4 class="mb-4">Ecoc Dashboard</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('dashboard') }}">Dashboard</a>
        </li>
        @if (auth()->user() && auth()->user()->isSuperAdmin())
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('tanks.settings') }}">Tank Settings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('tanks.create') }}">Add Tank</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('products.index') }}">Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('vessels.index') }}">Vessels</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('users.index') }}">Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('clients.index') }}">Clients</a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('transactions.create') }}">Transactions</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('profile') }}">Profile</a>
        </li>
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link text-white btn btn-link">Logout</button>
            </form>
        </li>
    </ul>
</div>

OLD SIDE BAR

<div class="main-sidebar-body">
    <ul class="nav">
        <li class="nav-header"><span class="nav-label">{{ trans('web_trans.dashboard') }}</span></li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}"><span class="shape1"></span><span class="shape2"></span><i class="ti-home sidemenu-icon"></i><span class="sidemenu-label">{{ trans('web_trans.dashboard') }}</span></a>
        </li>
        @if (Auth::user()->isSuperAdmin())
        <li class="nav-item">
            <a class="nav-link with-sub" href="#">
                <span class="shape1"></span>
                <span class="shape2"></span>
                <i class="ti-user sidemenu-icon"></i>
                <span class="sidemenu-label">{{ trans('web_trans.admins') }}</span>
                <i class="angle fe fe-chevron-right"></i>
            </a>
            <ul class="nav-sub">
                <li class="nav-sub-item">
                    <a class="nav-sub-link" href="{{ route('users.index') }}">{{ trans('web_trans.admin_list') }}</a>
                </li>
                <li class="nav-sub-item">
                    <a class="nav-sub-link" href="
                    {{-- {{ route('roles.index') }} --}}
                     ">Roles</a>
                </li>
            </ul>
        </li>
        @endif

        @if (Auth::user()->isSuperAdmin())
        <li class="nav-item">
            <a class="nav-link with-sub" href="#0">
                <span class="shape1"></span>
                <span class="shape2"></span>
                <i class="ti-user sidemenu-icon"></i>
                <span class="sidemenu-label">{{ trans('web_trans.users_management') }}</span>
                <i class="angle fe fe-chevron-right"></i>
            </a>
            <ul class="nav-sub">
                <li class="nav-sub-item">
                    <a class="nav-sub-link" href="{{ route('users.index') }}">{{ trans('web_trans.users_list') }}</a>
                </li>
            </ul>
        </li>
        @endif


        @if (Auth::user()->isSuperAdmin())
        <li class="nav-item">
            <a class="nav-link with-sub" href="#0">
                <span class="shape1"></span>
                <span class="shape2"></span>
                <i class="ti-list sidemenu-icon"></i>
                <span class="sidemenu-label">{{ trans('web_trans.Categories') }}</span>
                <i class="angle fe fe-chevron-right"></i>
            </a>
            <ul class="nav-sub">
                <li class="nav-sub-item">
                    <a class="nav-sub-link" href="
                    {{-- {{ route('categories.index') }} --}}
                     ">{{ trans('web_trans.Category_list') }}</a>
                </li>
            </ul>
        </li>
        @endif

        {{-- @if ((Auth::user()->isSuperAdmin() && Auth::user()->can('Setting')) || Auth::user()->isSuperAdmin())
        <li class="nav-item">
            <a class="nav-link with-sub" href="#0">
                <span class="shape1"></span>
                <span class="shape2"></span>
                <i class="ti-settings sidemenu-icon"></i>
                <span class="sidemenu-label">{{ trans('web_trans.settings') }}</span>
        <i class="angle fe fe-chevron-right"></i>
        </a>
        <ul class="nav-sub">
            @foreach ($setting_groups as $group)
            <li class="nav-sub-item">
                <a class="nav-sub-link" href="{{ route('settings.show', $group->name) }}">{{ $group->name }}</a>
            </li>
            @endforeach
        </ul>
        </li>
        @endif --}}
    </ul>
</div>