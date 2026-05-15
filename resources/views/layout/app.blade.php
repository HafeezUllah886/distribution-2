<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>

    <meta charset="utf-8" />
    <title>Business Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Business Management System" name="description" />
    <meta content="Hafeez Ullah" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/toastify/toastify.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .notification-dropdown {
            overflow-x: hidden !important;
        }
        .notification-list {
            overflow-x: hidden !important;
        }
        .notification-list .dropdown-item {
            overflow: hidden !important;
        }
    </style>


    @yield('page-css')

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ route('dashboard') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <h3 class="text-white">{{ projectNameShort() }}</h3>
                                </span>
                                <span class="logo-lg">
                                    <h3 class="text-white mt-3">{{ projectNameHeader() }}</h3>
                                </span>
                            </a>
                            <!-- Light Logo-->
                            <a href="{{ route('dashboard') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <h3 class="text-white">{{ projectNameShort() }}</h3>
                                </span>
                                <span class="logo-lg">
                                    <h3 class="text-white mt-3">{{ projectNameHeader() }}</h3>
                                </span>
                            </a>
                        </div>


                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>

                        </button>
                        <h3 class="mt-4">{{ Auth()->user()->branch->name }}</h3>
                    </div>
                    <div class="d-flex align-items-center">

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                                data-toggle="fullscreen">
                                <i class='bx bx-fullscreen fs-22'></i>
                            </button>
                        </div>
                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle light-dark-mode">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>
                        <div class="dropdown ms-1 header-item">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle position-relative"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                onclick="loadNotifications()">
                                <i class='bx bx-bell fs-22'></i>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge"
                                    style="display: none;">
                                    <span class="notification-count">0</span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg p-0 notification-dropdown"
                                style="width: 320px; max-height: 450px; overflow-y: auto; overflow-x: hidden;">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="m-0">Notifications</h6>
                                    <button class="btn btn-sm btn-link p-0" onclick="markAllRead()">Mark all
                                        read</button>
                                </div>
                                <div class="notification-list" style="overflow-x: hidden;">
                                    <div class="text-center p-3 text-muted">Loading...</div>
                                </div>
                                <div class="p-2 border-top text-center">
                                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light">View
                                        All</a>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user"
                                        src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->name }}</span>
                                        <span
                                            class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ auth()->user()->role }}</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}!</h6>
                                <a class="dropdown-item" href="{{ route('profile') }}"><i
                                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle">Profile</span></a>
                                <a class="dropdown-item" href="{{ route('logout') }}"><i
                                        class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle" data-key="t-logout">Logout</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========== App Menu ========== -->
        @if (auth()->user()->role == 'Admin')
            @include('layout.sidebar')
        @elseif (auth()->user()->role == 'Operator')
            @include('layout.operator_sidebar')
        @elseif (auth()->user()->role == 'Branch Admin')
            @include('layout.branch_admin_sidebar')
        @elseif (auth()->user()->role == 'Accountant')
            @include('layout.accountant_sidebar')
        @endif
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © BMS.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by Diamond Softwares
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <div class="customizer-setting d-none d-md-block">
        <div class="btn-info rounded-pill shadow-lg btn btn-icon btn-lg p-2" data-bs-toggle="offcanvas"
            data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
            <i class='mdi mdi-spin mdi-cog-outline fs-22'></i>
        </div>
    </div>

    <!-- Theme Settings -->
    @include('layout.settings')

    <!-- JAVASCRIPT -->

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    {{--     <script src="{{ asset('assets/js/plugins.js') }}"></script> --}}

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/libs/toastify/toastify.min.js') }}"></script>




    @if (Session::get('success'))
        <script>
            Toastify({
                text: "{{ Session::get('success') }}",
                className: "info",
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "center", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: "linear-gradient(to right, #01CB3E, #96c93d)",
                }
            }).showToast();
        </script>
    @endif
    @if (Session::get('error'))
        <script>
            Toastify({
                text: "{{ Session::get('error') }}",
                className: "info",
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "center", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: "linear-gradient(to right, #FF5733, #E70000)",
                }
            }).showToast();
        </script>
    @endif
    <script>
        function newWindow(route) {
            var width = screen.width;
            var height = screen.height;

            window.open(route, '_blank', `width=${width},height=${height}`);
        }

        function newWindowMobile(route) {
            var width = 412;
            var height = screen.height;

            window.open(route, '_blank', `width=${width},height=${height}`);
        }

        // Notifications
        function loadNotifications() {
            $.get('{{ route('notifications.list') }}', function(response) {
                var notifications = response.notifications;
                var count = response.unread_count;

                // Update badge
                if (count > 0) {
                    $('.notification-badge').show();
                    $('.notification-count').text(count > 9 ? '9+' : count);
                } else {
                    $('.notification-badge').hide();
                }

                // Build list
                if (notifications.length > 0) {
                    var html = '';
                    notifications.forEach(function(n) {
                        var iconClass = 'info';
                        var icon = 'bx-info-circle';
                        if (n.type === 'success') {
                            iconClass = 'success';
                            icon = 'bx-check-circle';
                        } else if (n.type === 'error') {
                            iconClass = 'danger';
                            icon = 'bx-x-circle';
                        } else if (n.type === 'warning') {
                            iconClass = 'warning';
                            icon = 'bx-exclamation-circle';
                        }

                        var unreadClass = n.status === 'unread' ? 'bg-light' : '';

                        html += `
                            <div class="dropdown-item notify-item ${unreadClass} py-2" onclick="markNotificationRead(${n.id})" style="white-space: normal; display: flex; align-items: flex-start;">
                                <div class="notify-icon bg-${iconClass} rounded-circle flex-shrink-0 me-2" style="width:32px; height:32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bx ${icon} text-white"></i>
                                </div>
                                <div class="notify-content" style="min-width: 0; max-width: 100%; overflow: hidden;">
                                    <p class="notify-subject mb-1 fw-semibold" style="white-space: normal; word-wrap: break-word; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${n.title}</p>
                                    <p class="text-muted font-12 mb-1" style="white-space: normal; word-wrap: break-word; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${n.message}</p>
                                    <p class="text-muted font-10 mb-0">${formatDate(n.created_at)}</p>
                                </div>
                            </div>
                        `;
                    });
                    $('.notification-list').html(html);
                } else {
                    $('.notification-list').html('<div class="text-center p-3 text-muted">No notifications</div>');
                }
            });
        }

        function formatDate(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var diff = now - date;
            var minutes = Math.floor(diff / 60000);
            var hours = Math.floor(diff / 3600000);
            var days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'Just now';
            if (minutes < 60) return minutes + ' min ago';
            if (hours < 24) return hours + ' hr ago';
            if (days < 7) return days + ' days ago';
            return date.toLocaleDateString();
        }

        function markNotificationRead(id) {
            $.post('{{ route('notifications.mark-as-read', ':id') }}'.replace(':id', id), {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                loadNotifications();
            });
        }

        function markAllRead() {
            $.post('{{ route('notifications.mark-all-read') }}', {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                loadNotifications();
            });
        }

        // Check for new notifications periodically
        $(document).ready(function() {
            loadNotifications();
            setInterval(loadNotifications, 30000); // Check every 30 seconds
        });
    </script>

    @yield('page-js')
</body>

</html>
