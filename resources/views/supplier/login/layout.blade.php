<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        {{config('app.name')}}
    </title>
    <!-- Favicon -->
    <link href="{{ asset('assets/img/brand/favicon.ico') }}" rel="icon" type="image/png">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Icons -->
    <link href="{{ asset('assets/css/nucleo/css/nucleo.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link href="{{ asset('assets/css/argon-dashboard.css?v=1.1.1') }}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sweetalert.css') }}">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" />

    <script src="https://kit.fontawesome.com/7b818e6d8e.js" crossorigin="anonymous"></script>

    <style>
        .input-group .form-control:not(:first-child) {
            padding-left: 10px;
        }

    </style>
</head>

<body class='bg-body-custom'>
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-top navbar-horizontal navbar-expand-md navbar-dark">
            <div class="container px-4">
                <button class="navbar-toggler custom-toggle" type="button" data-toggle="collapse"
                    data-target="#navbar-collapse-main" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon "></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar-collapse-main">
                    <!-- Collapse header -->
                    <div class="navbar-collapse-header d-md-none">
                        <div class="row">
                            <div class="col-6 collapse-brand">
                                <a href="{{ route('supplier.dashboard') }}">
                                    <img src="{{ asset('assets/img/brand/logo.png?v=2') }}">
                                </a>
                            </div>
                            <div class="col-6 collapse-close">
                                <button type="button" class="navbar-toggler" data-toggle="collapse"
                                    data-target="#navbar-collapse-main" aria-controls="sidenav-main"
                                    aria-expanded="false" aria-label="Toggle sidenav">
                                    <span></span>
                                    <span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Navbar items -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link nav-link-icon" href="{{ route('supplier.login.register') }}">
                                <i class="ni ni-circle-08"></i>
                                <span class="nav-link-inner--text">{{ trans('supplier.button_create_account_nav') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-icon" href="{{ route('supplier.login') }}">
                                <i class="ni ni-key-25"></i>
                                <span class="nav-link-inner--text">{{ trans('supplier.text_login') }}</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" style="font-size: inherit;">
                            {{ trans('supplier.button_language_nav') }} <span class="nav-link-inner--text"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink" id="language-selector">
                                <a class="dropdown-item" href="#" data-lang="zh">普通话</a>
                                    <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-lang="pt-br">Português</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="header bg-gradient-white py-7 pb-lg-8 pt-lg-5">
            <div class="container">
                <div class="header-body text-center mb-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-6 mb-5">
                            <img src="{{ asset('assets/img/brand/logo.png?v=2') }}"
                                class="img-fluid img-login-custom" />
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @yield('content')

        <footer class="py-5">
            <div class="container">
                <div class="row align-items-center justify-content-xl-between">
                    <div class="col-xl-12">
                        <div class="copyright text-center text-xl-left text-muted">
                            © {{ trans('supplier.text_year') }} <a href="#" class="font-weight-bold text-white ml-1" target="_blank">{{config('app.name')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!--   Core   -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <!--   Optional JS   -->
    <!--   Argon JS   -->
    <script src="{{ asset('assets/js/argon-dashboard.min.js?v=1.1.0') }}"></script>
    <script src="https://cdn.trackjs.com/agent/v3/latest/t.js"></script>
    <script src="{{ asset('assets/js/sweetalert.min.js') }}" type="text/javascript"></script>
    <script>
        window.TrackJS &&
            TrackJS.install({
                token: "ee6fab19c5a04ac1a32a645abde4613a",
                application: "argon-dashboard-free"
            });

        $(document).ready(function() {
            $('input').attr('readonly', false);
        })
    </script>

    @if (session('success'))
        <script type="text/javascript">
            swal("{{ trans('supplier.text_success_register') }}", "{{ session('success') }}", 'success');

        </script>
    @endif
    @if (session('error'))
        <script type="text/javascript">
            swal("{{ trans('supplier.oops') }}", "{{ session('error') }}", 'error');

        </script>
    @endif
    @if (session('warning'))
        <script type="text/javascript">
            swal("{{ trans('supplier.atencao') }}", "{{ session('warning') }}", 'warning');

        </script>
    @endif
    @if (session('info'))
        <script type="text/javascript">
            swal("{{ trans('supplier.info') }}:", "{{ session('info') }}");

        </script>
    @endif
    <script>
    $(document).ready(function() {
        $('#language-selector a').click(function(event) {
            event.preventDefault();
            var lang = $(this).data('lang');
            var date = new Date();
            date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
            document.cookie = "lang=" + lang + expires + "; path=/";
            location.reload();
        });
    });
    </script>
</body>

</html>
