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
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('assets_site/icon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets_site/icon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets_site/icon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets_site/icon/favicon-16x16.png') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    @yield('stylesheets_before')


<!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Icons -->
    <link href="{{ asset('assets/css/nucleo/css/nucleo.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link href="{{ asset('assets/css/argon-dashboard.css?v=1.1.3') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.css?v=1.1.2') }}" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sweetalert.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

    <script src="https://kit.fontawesome.com/7b818e6d8e.js" crossorigin="anonymous"></script>

    @yield('stylesheets')
    @if(url()->current() == 'https://painel.mawapost.com/shop' || url()->current() == 'http://localhost:8000/shop')
        @include('shop.layout.pixel-facebook')
    @endif
</head>

<body class="">
    @include('shop.layout.default.navbar')

    <div class="main-content">
        @include('shop.layout.default.header')

        @yield('content')

        <div class="container-fluid">
            @include('shop.layout.default.footer')
        </div>
    </div>

    <div class="modal fade" role="dialog" tabindex="-1" id="upload-receipt-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="" id="uploadReceiptForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="status" value="sent">
                    <div class="modal-header mt-0">
                        <h5 class="modal-title">{{ trans('supplier.notas_fiscais_title') }}</h5>
                    </div>
                    <div class="modal-body mb-0">
                        <p>Efetue o upload da nota fiscal deste pedido para disponibilizá-la ao seu cliente. Você também pode fazer o download da nota fiscal de remessa e venda disponibilizadas pelo fornecedor.</p>
                        <div class="form-group" id="shipping-receipt-div">
                            <label class="control-label">{{ trans('supplier.notas_fiscais_remessa_title') }}</label>
                            <span class="d-block" id="shipping-receipt-link"></span>
                        </div>
                        <div class="form-group" id="order-receipt-div">
                            <label class="control-label">{{ trans('supplier.notas_fiscais_venda_title') }}</label>
                            <span class="d-block" id="order-receipt-link"></span>
                        </div>
                        <div class="form-group" id="customer-receipt-div">
                            <label class="control-label">Nota Fiscal para o Cliente</label>
                            <span class="d-block" id="customer-receipt-link"></span>
                            <input type="file" id="customer-receipt-input" class="form-control" name="customer_receipt" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('supplier.cancel') }}</button>
                        <button class="btn btn-success" id="customer-receipt-button">{{ trans('supplier.efetuar_upload_title') }}</button>
                    </div>
                </form>
            </div>
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
    <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    {{-- Jquery ui --}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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

        $("[tooltip='true']").tooltip({
            boundary: 'window'
        })

        function uploadReceipt(route, order_id){
            $("#uploadReceiptForm").attr('action', route);

            $.ajax({
                url: '/shop/orders/' + order_id + '/get_json',
                success: function(order){
                    let order_receipt = order.receipts.find(receipt => receipt.type === 'order');
                    let shipping_receipt = order.receipts.find(receipt => receipt.type === 'shipping');
                    let customer_receipt = order.receipts.find(receipt => receipt.to === 'customer');

                    if(order_receipt){
                        $("#order-receipt-div").show();
                        $("#order-receipt-link").html('<a class="btn btn-info btn-sm" href="/shop/orders/download_receipt/'+ order_receipt.id +'">' + download_nota_venda_title + '</a>');
                    }
                    if(shipping_receipt){
                        $("#shipping-receipt-div").show();
                        $("#shipping-receipt-link").html('<a class="btn btn-info btn-sm" href="/shop/orders/download_receipt/'+ shipping_receipt.id +'">' + download_nota_remessa_title + '</a>');
                    }
                    if(customer_receipt){
                        $("#customer-receipt-input").hide();
                        $("#customer-receipt-button").hide();
                        $("#customer-receipt-link").html('<a class="btn btn-info btn-sm" href="/shop/orders/download_receipt/'+ customer_receipt.id +'">Download da nota do cliente</a>');
                    }
                },
                error: function(response){
                    //console.log(response);
                }
            });
        }
    </script>

    <script>
        $(document).ready(function(){
            $('#copy-pix-key').on('click', function(){
                let copy = document.getElementById('key-pix');
                copy.select();
                document.execCommand("Copy");

                // $('#key-pix').html().select()
                // document.execCommand('copy');
            })

            
        })
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
