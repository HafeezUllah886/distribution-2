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

    <!-- Layout config Js -->
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
    @yield('page-css')

</head>

<body>

    <div class="page-content" style="padding: 10px !important">
        <div class="container-fluid">

            @yield('content')

        </div>
        <!-- container-fluid -->
    </div>

    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
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

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    @if (Session::get('success'))
    <script>
       Toastify({
        text: "{{Session::get('success')}}",
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
        text: "{{Session::get('error')}}",
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
         $(document).on('keydown', function(event) {
    // Check if F2 is pressed
    if (event.key === 'F2') {
        event.preventDefault();  // Prevent any default action (if any)
        $('#code').focus();  // Focus on the input field
    }
});
    </script>
    @yield('page-js')
</body>

</html>
