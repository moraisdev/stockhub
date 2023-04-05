<!--

=========================================================
* Argon Dashboard - v1.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2019 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://github.com/creativetimofficial/argon-dashboard/blob/master/LICENSE.md)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        @yield('title')
    </title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets_site/icon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets_site/icon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets_site/icon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets_site/icon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets_site/icon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets_site/icon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets_site/icon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets_site/icon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets_site/icon/apple-icon-180x180.png') }}">
    <link href="{{ asset('assets/img/brand/favicon.ico') }}" rel="icon" type="image/png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Icons -->
    <link href="{{ asset('assets/css/nucleo/css/nucleo.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link href="{{ asset('assets/css/argon-dashboard.css?v=1.1.1') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sweetalert.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

    <script src="https://kit.fontawesome.com/7b818e6d8e.js" crossorigin="anonymous"></script>

    @yield('stylesheets')
</head>

<body class="">
    @include('admin.layout.default.navbar')

    <div class="main-content">
        @include('admin.layout.default.header')

        @yield('content')

        <div class="container-fluid">
            @include('admin.layout.default.footer')
        </div>
    </div>

    <div class="modal fade" role="dialog" tabindex="-1" id="modal-products-shop-choice">
        <div class="modal-dialog" role="document">
            <form action="" method="POST" id="form-delete">
                @csrf
                @method('post')
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">{{ trans('supplier.escolha_fornecedor') }}</h3>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans('supplier.confirm_excluir_registro') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('supplier.cancel') }}</button>
                        <button class="btn btn-danger">{{ trans('supplier.delete') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" role="dialog" tabindex="-1" id="modal-delete">
        <div class="modal-dialog" role="document">
            <form action="" method="POST" id="form-delete">
                @csrf
                @method('post')
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">{{ trans('supplier.excluir_registro') }}</h3>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans('supplier.confirm_excluir_registro') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('supplier.cancel') }}</button>
                        <button class="btn btn-danger">{{ trans('supplier.delete') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--   Core   -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <!--   Optional JS   -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    <!--   Argon JS   -->
    <script src="{{ asset('/assets/js/argon-dashboard.js?v=1.1.0') }}"></script>

    <script src="https://cdn.trackjs.com/agent/v3/latest/t.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>

    <script>
        window.TrackJS &&
        TrackJS.install({
            token: "ee6fab19c5a04ac1a32a645abde4613a",
            application: "argon-dashboard-free"
        });

        toastr.options = {
            "debug": false,
            "positionClass": "toast-bottom-left",
        }

        $(".decimal").maskMoney({thousands:''});
        $(".cep").mask('99999-999');
        $(".phone").mask('(99) 99999-9999');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("[tooltip='true']").tooltip({boundary: 'window'});

        function updateDeleteModal(route){
            $("#form-delete").attr('action', route);
        }
    </script>

    @yield('scripts')

    @if(session('success'))
        <script type="text/javascript">
            Swal.fire("{{ trans('supplier.text_success_register') }}", "{{ session('success') }}", 'success');
        </script>
    @endif
    @if(session('error'))
        <script type="text/javascript">
            Swal.fire("Erro", "{{ session('error') }}", 'error');
        </script>
    @endif
    @if(session('warning'))
        <script type="text/javascript">
            Swal.fire("{{ trans('supplier.atencao') }}", "{{ session('warning') }}", 'warning');
        </script>
    @endif
    @if(session('info'))
        <script type="text/javascript">
            Swal.fire("Info:", "{{ session('info') }}");
        </script>
    @endif

    @if($errors->any())
        <?php
            $error_list = "<ul>";

            foreach($errors->all() as $error){
                $error_list .= "<li>".$error.'</li>';
            }

            $error_list .= "</ul>";
        ?>

        <script type="text/javascript">
            Swal.fire("Validation error", "{!! $errors->first() !!}", 'error');
        </script>
    @endif
    @if(session('success_notification'))
        <script type="text/javascript">
            toastr.success("{{ trans('supplier.text_success_register') }}", "{{ session('success_notification') }}");
        </script>
    @endif
    @if(session('error_notification'))
        <script type="text/javascript">
            toastr.error("Erro", "{{ session('error_notification') }}");
        </script>
    @endif
    @if(session('warning_notification'))
        <script type="text/javascript">
            toastr.warning("{{ trans('supplier.atencao') }}", "{{ session('warning_notification') }}");
        </script>
    @endif
    @if(session('info_notification'))
        <script type="text/javascript">
            toastr.info("Info:", "{{ session('info_notification') }}");
        </script>
    @endif
</body>

</html>
