<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('title')</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
    <link href="{{ asset('assets/img/logo.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- jQuery JS -->
    <script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>


    <!-- for datatable CSS File -->
    <link href="https://cdn.datatables.net/2.1.2/css/dataTables.dataTables.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/searchpanes/2.3.1/css/searchPanes.dataTables.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/2.0.3/css/select.dataTables.css" rel="stylesheet" />
    <link href=" https://cdn.datatables.net/fixedcolumns/5.0.1/css/fixedColumns.dataTables.css" rel="stylesheet" />
    <link href=" https://cdn.datatables.net/buttons/3.1.0/css/buttons.dataTables.css" rel="stylesheet" />


    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet"> --}}


    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    {{-- link custome css --}}
    <link href="{{ asset('assets/css/custome.css') }}" rel="stylesheet">

    {{-- font-awesome --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="..."> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Select2 CSS File -->
    <link href="{{ asset('select2/select2.min.css') }}" rel="stylesheet" />


    <!-- Additional Scripts -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('sweet_alert/sweetalert.js') }}"></script>
    <script src="{{ asset('assets/js/ajax.js') }}"></script>

    {{-- for chart.js --}}
    <script src="{{ asset('assets/js/chart.min.js') }}"></script>

    @stack('styles')



</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="">
                {{-- <span class="d-none d-lg-block ">SINGHAL STEEL</span> --}}

            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->


        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">


                <li class="nav-item dropdown pe-3">

                    {{-- ==================== NOTIFICATION BELL ==================== --}}
                    <li class="nav-item dropdown" id="notification-dropdown">
                        <a class="nav-link nav-icon position-relative d-inline-flex align-items-center justify-content-center"
                        href="#" data-bs-toggle="dropdown" aria-expanded="false"
                        style="width: 40px; height: 40px;">
                            <i class="bi bi-bell fs-5"></i>

                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger rounded-pill position-absolute notification-badge"
                                    style="top: -2px; right: -2px; font-size: 0.65rem; min-width: 18px; height: 18px; display: flex; align-items: center; justify-content: center;">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications p-0 shadow-lg"
                            style="width: 380px; max-height: 80vh; overflow-y: auto;">

                            <!-- Header -->
                            <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-3 bg-primary text-white">
                                <strong>Notifications ({{ auth()->user()->unreadNotifications->count() }})</strong>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <a href="#" id="mark-all-read" class="text-white small fw-bold opacity-90">
                                        Mark all as read
                                    </a>
                                @endif
                            </li>

                            <!-- Notification List -->
                            <div id="notification-list">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <li class="notification-item dropdown-item px-3 py-3 border-bottom hover-bg-light d-flex align-items-start"
                                        data-id="{{ $notification->id }}"
                                        style="cursor: pointer; transition: background 0.2s;">
                                        <div class="me-3">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white"
                                                style="width: 38px; height: 38px; flex-shrink: 0;">
                                                <i class="bi bi-bell-fill fs-5"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                            <p class="mb-1 small text-muted lh-sm">{{ $notification->data['message'] ?? '' }}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </li>
                                @empty
                                    <li class="dropdown-item text-center py-5 text-muted">
                                        <i class="bi bi-bell-slash fs-1 d-block mb-3 opacity-50"></i>
                                        <p class="mb-0 fw-medium">No new notifications</p>
                                    </li>
                                @endforelse
                            </div>
                        </ul>
                    </li>
                    {{-- ==================== END NOTIFICATION BELL ==================== --}}

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                        data-bs-toggle="dropdown">
                        @if (Auth::user()->profile)
                            <img src="{{ asset('uploads/user_profile/' . Auth::user()->id . '/' . Auth::user()->profile) }}"
                                alt="Profile">
                        @else
                            <img src="{{ asset('assets/img/profile-img.png') }}" alt="Profile">
                        @endif
                        <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}
                            {{ Auth::user()->last_name }}</span>
                    </a>


                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">


                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile_edit') }}">
                                <i class="bi bi-person"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->



    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            @can('Dashboard')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            @endcan

            {{-- Dealers and Distributors --}}
            @php
                $ddControlActive =
                    request()->routeIs('distributors.index') ||
                    request()->routeIs('dealers.index') ||
                    request()->routeIs('distributors.create') ||
                    request()->routeIs('distributor_team.index') ||
                    request()->routeIs('distributor_team.create');
            @endphp

            {{-- @can('DealerDistributor-Module') --}}
            {{-- @if (auth()->check() && auth()->user()->can('Dealers-Index')) --}}
            @if (auth()->check() &&
                    (auth()->user()->can('Dealers-Improved') ||
                        auth()->user()->can('Distributors-Index') ||
                        auth()->user()->can('DistributorsTeam-Index')))
                <li class="nav-item">
                    <a class="nav-link  {{ $ddControlActive ? '' : 'collapsed' }}"
                        data-bs-target="#dealers-distributors-nav" data-bs-toggle="collapse" href="#">
                        <i class="fa-solid fa-globe"></i><span>Dealers & Distributors</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="dealers-distributors-nav" class="nav-content collapse {{ $ddControlActive ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        @can('Dealers-Index')
                            <li>
                                <a class="{{ request()->routeIs('dealers.index') ? 'active' : '' }}"
                                    href="{{ route('dealers.index') }}">
                                    <i class="bi bi-circle"></i><span>Dealers</span>
                                </a>
                            </li>
                        @endcan
                        @can('Distributors-Index')
                            <li>
                                <a class="{{ request()->routeIs('distributors.index') ? 'active' : '' }}"
                                    href="{{ route('distributors.index') }}">
                                    <i class="bi bi-circle"></i><span>Distributors</span>
                                </a>
                            </li>
                        @endcan
                        @can('DistributorsTeam-Index')
                            <li>
                                <a class="{{ request()->routeIs('distributor_team.index') ? 'active' : '' }}"
                                    href="{{ route('distributor_team.index') }}">
                                    <i class="bi bi-circle"></i><span>Distributor Team</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            {{-- @endcan --}}
            @php
                $ordercontrol = request()->routeIs('order_management') || request()->routeIs('order_management.create');
            @endphp

            @can('Order-Index')
                <li class="nav-item">
                    <a class="nav-link {{ $ordercontrol ? '' : 'collapsed' }}" href="{{ route('order_management') }}">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Order Management</span>
                    </a>
                </li>
            @endcan



            @php
                $dispatchControl = request()->routeIs('dispatch.index') || request()->routeIs('dispatch.create');
            @endphp

            @can('Dispatch-Index')
                <li class="nav-item">
                    <a class="nav-link {{ $dispatchControl ? '' : 'collapsed' }}" href="{{ route('dispatch.index') }}">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                        <span>Dispatch</span>
                    </a>
                </li>
            @endcan



            {{-- Md Raza Changes starts here --}}

            {{-- ------------------Reports----------------- --}}
            {{-- @can('Report-Module') --}}
            @if (auth()->check() &&
                    (auth()->user()->can('OrderReport-Index') ||
                        auth()->user()->can('DispatchReport-Index') ||
                        auth()->user()->can('ItemPriceReport-Index') ||
                        auth()->user()->can('ItemSizeReport-Index') ||
                        auth()->user()->can('DistributorTeamReport-Index') ||
                        auth()->user()->can('DistributorReport-Index') ||
                        auth()->user()->can('DealerReport-Index') ||
                        auth()->user()->can('TopPerformerReport-Index')))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('order.report') ||
                    request()->routeIs('dispatch.report') ||
                    request()->routeIs('item_price.report') ||
                    request()->routeIs('distributor_team.report') ||
                    request()->routeIs('dealers.report') ||
                    request()->routeIs('distributors.report') ||
                    request()->routeIs('item_sizes.report') ||
                    request()->routeIs('top_performers.index')
                        ? ''
                        : 'collapsed' }}"
                        data-bs-target="#reports-nav" data-bs-toggle="collapse" href="#">
                        <i class="fa-solid fa-boxes-packing"></i><span>Reports</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>

                    <ul id="reports-nav"
                        class="nav-content collapse {{ request()->routeIs('order.report') ||
                        request()->routeIs('dispatch.report') ||
                        request()->routeIs('item_price.report') ||
                        request()->routeIs('distributor_team.report') ||
                        request()->routeIs('dealers.report') ||
                        request()->routeIs('distributors.report') ||
                        request()->routeIs('item_sizes.report') ||
                        request()->routeIs('top_performers.index')
                            ? 'show'
                            : '' }}"
                        data-bs-parent="#sidebar-nav">
                        @can('TopPerformerReport-Index')
                            <li>
                                <a class="{{ request()->routeIs('top_performers.index') ? 'active' : '' }}"
                                    href="{{ route('top_performers.index') }}">
                                    <i class="bi bi-circle"></i><span>Top Performers Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('DealerReport-Index')
                            <li>
                                <a class="{{ request()->routeIs('dealers.report') ? 'active' : '' }}"
                                    href="{{ route('dealers.report') }}">
                                    <i class="bi bi-circle"></i><span>Dealers Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('DistributorReport-Index')
                            <li>
                                <a class="{{ request()->routeIs('distributors.report') ? 'active' : '' }}"
                                    href="{{ route('distributors.report') }}">
                                    <i class="bi bi-circle"></i><span>Distributors Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('DistributorTeamReport-Index')
                            <li>
                                <a class="{{ request()->routeIs('distributor_team.report') ? 'active' : '' }}"
                                    href="{{ route('distributor_team.report') }}">
                                    <i class="bi bi-circle"></i><span>Distributor Team Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('OrderReport-Index')
                            <li>
                                <a class="{{ request()->routeIs('order.report') ? 'active' : '' }}"
                                    href="{{ route('order.report') }}">
                                    <i class="bi bi-circle"></i><span>Order Management Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('DispatchReport-Index')
                            <li>
                                <a class="{{ request()->routeIs('dispatch.report') ? 'active' : '' }}"
                                    href="{{ route('dispatch.report') }}">
                                    <i class="bi bi-circle"></i><span>Dispatch Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('ItemSizeReport-Index')
                            <li>
                                <a class="{{ request()->routeIs('item_sizes.report') ? 'active' : '' }}"
                                    href="{{ route('item_sizes.report') }}">
                                    <i class="bi bi-circle"></i><span>Item Sizes Report</span>
                                </a>
                            </li>
                        @endcan
                        @can('ItemPriceReport-Index')
                            <li>
                                <a class="{{ request()->routeIs('item_price.report') ? 'active' : '' }}"
                                    href="{{ route('item_price.report') }}">
                                    <i class="bi bi-circle"></i><span>Item Basic Prices Report</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            {{-- @endcan --}}

            {{-- Md Raza Changes ends here --}}


            {{-- Item Master --}}
            {{-- @can('ItemName-Module') --}}
            @if (auth()->check() &&
                    (auth()->user()->can('ItemName-Index') ||
                        auth()->user()->can('ItemSize-Index') ||
                        auth()->user()->can('ItemBasicPrice-Index') ||
                        auth()->user()->can('ItemBundle-Index')))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('items.index') || request()->routeIs('itemBundle.index') || request()->routeIs('itemSizes.index') || request()->routeIs('itemBasicPrice.index') ? '' : 'collapsed' }}"
                        data-bs-target="#item-master-nav" data-bs-toggle="collapse" href="#">
                        <i class="fa-solid fa-boxes-packing"></i><span>Item Master</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="item-master-nav"
                        class="nav-content collapse {{ request()->routeIs('itemBundle.index') || request()->routeIs('items.index') || request()->routeIs('itemSizes.index') || request()->routeIs('itemBasicPrice.index') ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">

                        {{-- @can('ItemName-Index')
                            <li>
                                <a class="{{ request()->routeIs('items.index') ? 'active' : '' }}"
                                    href="{{ route('items.index') }}">
                                    <i class="bi bi-circle"></i><span>Items</span>
                                </a>
                            </li>
                        @endcan --}}

                        @can('ItemSize-Index')
                            <li>
                                <a class="{{ request()->routeIs('itemSizes.index') ? 'active' : '' }}"
                                    href="{{ route('itemSizes.index') }}">
                                    <i class="bi bi-circle"></i><span>Item Sizes</span>
                                </a>
                            </li>
                        @endcan

                        @can('ItemBasicPrice-Index')
                            <li>
                                <a class="{{ request()->routeIs('itemBasicPrice.index') ? 'active' : '' }}"
                                    href="{{ route('itemBasicPrice.index') }}">
                                    <i class="bi bi-circle"></i><span>Item Basic Prices</span>
                                </a>
                            </li>
                        @endcan

                        {{-- @can('ItemBundle-Index')
                            <li>
                                <a class="{{ request()->routeIs('itemBundle.index') ? 'active' : '' }}"
                                    href="{{ route('itemBundle.index') }}">
                                    <i class="bi bi-circle"></i><span>Item Bundle</span>
                                </a>
                            </li>
                        @endcan --}}
                    </ul>
                </li>
            @endif
            {{-- @endcan --}}







            {{-- ------------------User Control----------------- --}}

            @php
                $userControlActive =
                    request()->routeIs('users.index') ||
                    request()->routeIs('users.create') ||
                    request()->routeIs('users.edit') ||
                    request()->routeIs('users.show') ||
                    request()->routeIs('roles.index') ||
                    request()->routeIs('roles.create') ||
                    request()->routeIs('roles.edit') ||
                    request()->routeIs('appUserManagement') ||
                    request()->routeIs('roles.show');
            @endphp
            @if (
                (auth()->check() && auth()->user()->can('UserManagement-Index')) ||
                    auth()->user()->can('AccessManagement-Index') ||
                    auth()->user()->can('AppUserMgmt-Index'))
                <li class="nav-item">
                    <a class="nav-link  {{ $userControlActive ? '' : 'collapsed' }}" data-bs-target="#icons-nav"
                        data-bs-toggle="collapse" href="#">
                        <i class="fa-solid fa-user-lock"></i><span>Users Control</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="icons-nav" class="nav-content collapse {{ $userControlActive ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        @can('UserManagement-Index')
                            <li>
                                <a class="{{ request()->routeIs('users.index', 'users.create', 'users.edit', 'users.show') ? 'active' : '' }}"
                                    href="{{ route('users.index') }}">
                                    <i class="bi bi-circle"></i><span>User Management</span>
                                </a>
                            </li>
                        @endcan
                        @can('AccessManagement-Index')
                            <li>
                                <a class="{{ request()->routeIs('roles.index', 'roles.create', 'roles.edit', 'roles.show') ? 'active' : '' }}"
                                    href="{{ route('roles.index') }}">
                                    <i class="bi bi-circle"></i><span>Access Management</span>
                                </a>
                            </li>
                        @endcan
                        @can('AppUserMgmt-Index')
                            <li>
                                <a class="{{ request()->routeIs('appUserManagement') ? 'active' : '' }}"
                                    href="{{ route('appUserManagement') }}">
                                    <i class="bi bi-circle"></i><span>App User Management</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif


            {{-- ------------------Setting----------------- --}}
            @php
                $settingControlActive =
                    request()->routeIs('email_setting.index') ||
                    request()->routeIs('gst-setting.index') ||
                    request()->routeIs('warehouse.index') ||
                    request()->routeIs('warehouse.create') ||
                    // request()->routeIs('loadingPointMaster.index') ||
                    // request()->routeIs('loadingPointMaster.create') ||
                    request()->routeIs('company');
            @endphp

            @if (
                (auth()->check() && auth()->user()->can('Company-Index')) ||
                    auth()->user()->can('Email-Index') ||
                    auth()->user()->can('Warehouse-Index'))
                <li class="nav-item">
                    <a class="nav-link  {{ $settingControlActive ? '' : 'collapsed' }}"
                        data-bs-target="#settings-nav" data-bs-toggle="collapse" href="#">
                        <i class="fa-solid fa-gear"></i><span>Settings</span><i
                            class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="settings-nav" class="nav-content collapse {{ $settingControlActive ? 'show' : '' }}"
                        data-bs-parent="#sidebar-nav">
                        @can('Company-Index')
                            <li>
                                <a class="{{ request()->routeIs('company') ? 'active' : '' }}"
                                    href="{{ route('company') }}">
                                    <i class="bi bi-circle"></i><span>Company Setting</span>
                                </a>
                            </li>
                        @endcan
                        {{-- @can('Email-Index')
                            <li>
                                <a class="{{ request()->routeIs('email_setting.index') ? 'active' : '' }}"
                                    href="{{ route('email_setting.index') }}">
                                    <i class="bi bi-circle"></i><span>Email</span>
                                </a>
                            </li>
                        @endcan --}}
                        {{-- @can('GST-Index')
                            <li>
                                <a class="{{ request()->routeIs('gst-setting.index') ? 'active' : '' }}"
                                    href="{{ route('gst-setting.index') }}">
                                    <i class="bi bi-circle"></i><span>GST Settings</span>
                                </a>
                            </li>
                        @endcan --}}

                        {{-- Warehouse --}}
                        @can('Warehouse-Index')
                            <li>
                                <a class="{{ request()->routeIs('warehouse.index') ? 'active' : '' }}"
                                    href="{{ route('warehouse.index') }}">
                                    <i class="bi bi-circle"></i><span>Warehouse</span>
                                </a>
                            </li>
                        @endcan

                        {{-- Loading Point Master --}}
                        {{-- @can('LoadingPoint-Index')
                            <li>
                                <a class="{{ request()->routeIs('loadingPointMaster.index') ? 'active' : '' }}"
                                    href="{{ route('loadingPointMaster.index') }}">
                                    <i class="bi bi-circle"></i><span>Loading Point Master</span>
                                </a>
                            </li>
                        @endcan --}}

                    </ul>
                </li>
            @endif

        </ul>
    </aside><!-- End Sidebar-->


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const notificationDropdown = document.getElementById('notification-dropdown');
            const notificationList = document.getElementById('notification-list');
            const markAllReadBtn = document.getElementById('mark-all-read');
            const noNotificationsMessage = '<li id="no-notifications-message" class="dropdown-item text-center text-muted py-4">No new notifications</li>';
            const notificationHeader = notificationDropdown?.querySelector('.dropdown-header');

            /**
             * Updates the notification badge count and visibility based on remaining unread items.
             * Also updates the "Mark all as read" button visibility.
             */
            function updateBadgeAndUI() {
                const remainingItems = document.querySelectorAll('#notification-list .notification-item').length;
                const badge = document.querySelector('.notification-badge');
                const dropdownToggle = notificationDropdown?.querySelector('.nav-link');

                if (remainingItems === 0) {
                    if (badge) {
                        badge.remove();
                    }
                    if (notificationList && !document.getElementById('no-notifications-message')) {
                        notificationList.innerHTML = noNotificationsMessage;
                    }
                    if (markAllReadBtn) {
                        markAllReadBtn.style.display = 'none';
                    }
                    if (notificationHeader) {
                        notificationHeader.innerHTML = '<strong>Notifications</strong>'; // Remove "Mark all as read" from header
                    }
                } else {
                    if (!badge && dropdownToggle) {
                        // Create badge if it doesn't exist
                        const newBadge = document.createElement('span');
                        newBadge.className = 'badge bg-danger rounded-pill position-absolute notification-badge';
                        newBadge.style.cssText = 'top: -2px; right: -2px; font-size: 0.65rem; min-width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; border-radius: 50%; z-index: 10;';
                        dropdownToggle.appendChild(newBadge);
                        badge = newBadge; // Update reference
                    }
                    if (badge) {
                        badge.textContent = remainingItems;
                    }
                    if (markAllReadBtn) {
                        markAllReadBtn.style.display = 'inline';
                    }
                    // Re-add "Mark all as read" to header if needed
                    if (notificationHeader && !notificationHeader.querySelector('#mark-all-read')) {
                        const markAllLink = document.createElement('a');
                        markAllLink.id = 'mark-all-read';
                        markAllLink.href = '#';
                        markAllLink.className = 'small text-muted';
                        markAllLink.textContent = 'Mark all as read';
                        notificationHeader.appendChild(markAllLink);
                        // Re-attach event listener for the new button
                        attachMarkAllReadListener();
                    }
                }
            }

            /**
             * Handles marking a single notification as read on the server and updates UI.
             */
            function markSingleRead(event) {
                event.preventDefault(); // Prevent any default behavior
                const item = this.closest('.notification-item');
                const id = item.dataset.id;

                if (!id) {
                    console.error('Notification ID not found');
                    return;
                }

                // Disable the item temporarily to prevent multiple clicks
                item.style.opacity = '0.5';
                item.style.pointerEvents = 'none';

                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (response.ok) {
                        item.remove();
                        updateBadgeAndUI();
                    } else {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    // Re-enable on error
                    item.style.opacity = '1';
                    item.style.pointerEvents = 'auto';
                    // Optional: Show user-friendly error (e.g., via SweetAlert)
                    if (typeof swal !== 'undefined') {
                        swal('Error', 'Failed to mark notification as read. Please try again.', 'error');
                    }
                });
            }

            /**
             * Attaches event listener for marking a single notification as read.
             */
            function attachSingleReadListeners() {
                document.querySelectorAll('#notification-list .notification-item').forEach(item => {
                    item.removeEventListener('click', markSingleRead); // Prevent duplicates
                    item.addEventListener('click', markSingleRead);
                });
            }

            /**
             * Handles marking all notifications as read.
             */
            function markAllRead(event) {
                event.preventDefault();

                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Clear the list and update UI
                        document.querySelectorAll('#notification-list .notification-item').forEach(el => el.remove());
                        notificationList.innerHTML = noNotificationsMessage;
                        updateBadgeAndUI();

                        // Close the dropdown after success (optional, for better UX)
                        const dropdown = bootstrap.Dropdown.getInstance(notificationDropdown.querySelector('[data-bs-toggle="dropdown"]'));
                        if (dropdown) {
                            dropdown.hide();
                        }
                    } else {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                    // Optional: Show user-friendly error
                    if (typeof swal !== 'undefined') {
                        swal('Error', 'Failed to mark all notifications as read. Please try again.', 'error');
                    }
                });
            }

            /**
             * Attaches event listener for "Mark all as read".
             */
            function attachMarkAllReadListener() {
                const btn = document.getElementById('mark-all-read');
                if (btn) {
                    btn.removeEventListener('click', markAllRead); // Prevent duplicates
                    btn.addEventListener('click', markAllRead);
                }
            }

            // Initial setup
            attachSingleReadListeners();
            attachMarkAllReadListener();
            updateBadgeAndUI(); // Ensure initial state is correct

            // Optional: Poll for new notifications every 30 seconds (requires backend endpoint /api/notifications/unread/count returning { count: N })
            // Uncomment and adjust if the API endpoint exists
            setInterval(() => {
                fetch('/api/notifications/unread/count', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    const newCount = data.count || 0;
                    if (newCount > 0) {
                        // If new notifications, reload the dropdown content via AJAX
                        fetch('/api/notifications/unread?limit=10', {
                            headers: { 'Accept': 'application/json' }
                        })
                        .then(r => r.json())
                        .then(notifications => {
                            if (notifications.length > 0) {
                                let html = '';
                                notifications.forEach(n => {
                                    html += `
                                        <li class="notification-item dropdown-item p-3 border-bottom hover-bg-light d-flex" data-id="${n.id}" style="cursor: pointer;">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">${n.data.title || 'Notification'}</h6>
                                                <p class="mb-1 small text-muted mb-2">${n.data.message || ''}</p>
                                                <small class="text-muted">${new Date(n.created_at).toLocaleString()}</small>
                                            </div>
                                        </li>
                                    `;
                                });
                                notificationList.innerHTML = html;
                                attachSingleReadListeners();
                            }
                            updateBadgeAndUI();
                        });
                    } else {
                        updateBadgeAndUI();
                    }
                })
                .catch(error => console.error('Error polling for notifications:', error));
            }, 30000);
        });
    </script>
@endpush
