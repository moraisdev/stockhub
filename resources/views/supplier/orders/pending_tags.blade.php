<!DOCTYPE html>
<html>
<head>
    <title>{{config('app.name')}} - {{ trans('supplier.etiquetas_pendetes') }}</title>

    <style type="text/css">
        body{
            margin:0;
            padding:10px;
        }

        .tag{
            width: 500px;
            height: 550px;
            border: 1px solid black;
            box-sizing: border-box;
        }

        .first-block{
            box-sizing: border-box;
            width: 100%;
            height: 250px;
            border-bottom: 1px solid black;
            padding: 25px;
        }

        .second-block{
            box-sizing: border-box;
            width: 100%;
            height: 200px;
            border-bottom: 1px solid black;
            padding: 25px;
        }

        .third-block{
            box-sizing: border-box;
            width: 100%;
            height: 100px;
            padding: 10px 25px;
        }

        .tag-header{
            height: 130px;
            display: flex;
            justify-content: space-between;
        }

        .row{
            display: flex;
            justify-content: space-between;
        }

        .header-block-1{
            display: flex;
            width: 115px;
            height: 100px;
            align-self: center;
        }

        .header-block-2{
            width: 130px;
            height: 130px;
        }

        .header-block-3{
            display: flex;
            width: 115px;
            height: 100px;
            align-self: center;
        }

        .tag-to{
            display: flex;
        }

        .from-col{
            flex: 60%;
        }

        .right-col{
            flex: 40%;
        }

        .full-col{
            flex: 100%;
        }

        .title{
            display: block;
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        .from{
            border-left: 1px solid black;
            padding-left: 5px;
        }

        .right-data{
            text-align: right;
        }

        .to{
            font-size: 1.3em;
        }

        .medium{
            font-size: 0.85em;
        }

        .small{
            font-size: 0.7em;
        }

        .w-100{
            width: 115px;
        }

        .w-160{
            width: 130px;
        }

        .line{
            display: inline;
            width: 100%;
            height: 1px;
            border-bottom: 1px solid black;
        }

        .justify-content-center{
            display: flex;
            justify-content: center !important;
        }

        .align-center{
            text-align: center;
        }

        @media print
        {
            .print-break{
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
@foreach($supplier_orders as $supplier_order)
    <div class="print-break">
        <h3>{{ $supplier_order->f_display_id }}</h3>
        <div class="tag">
            <div class="first-block">
                {{--<div class="tag-header">
                    <div class="header-block-1">
                        <img src="{{ asset('assets/img/brand/logo.png') }}" style="max-width:100%; max-height: 100%; align-self: center;">
                    </div>
                    <div class="header-block-2">
                        {!! echo DNS2D::getBarcodeSVG('https://mawapost.com', 'QRCODE', 5.2, 5.2) !!}
                    </div>
                    <div class="header-block-3">
                        <img src="{{ asset('assets/img/brand/logo.png') }}" style="max-width:100%; max-height: 100%; align-self: center;">
                    </div>
                </div>--}}
                @php
                    $display_id = str_pad($supplier_order->display_id, 8, "0", STR_PAD_LEFT);
                @endphp
                <div class="row" style="margin-top: 5px">
                    <div class="w-100 small">
                        {{--Nota fiscal: 12345678910<br>--}}
                        {{ trans('supplier.pedido') }}: #{{ $display_id }} <br>
                        {{--PLP: 1234567890--}}
                    </div>
                    <div class="w-160 small">
                        {{--Contrato: MP{{ $order_id }}--}}
                    </div>
                    <div class="w-100 small">
                        {{--Volume 1/1--}}
                    </div>
                </div>
                <div class="row" style="margin-top: 5px">
                    <div class="full-col align-center" style="margin-left: 25px; margin-right: 25px; margin-top: 10px; height: 95px">
                        <b style="display:block">{{ $display_id }}</b>
                        <?php echo DNS1D::getBarcodeSVG("$display_id", 'UPCA', 4, 75, 0, false); ?>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px">
                    <div class="small">
                        <span style="display: block; margin-bottom: 10px;">{{ trans('supplier.recebedor') }}: __________________________________________________________________</span>
                        <span style="display: block">{{ trans('supplier.assinatura_title') }}: _____________________________ {{ trans('supplier.documento') }}: __________________________</span>
                    </div>
                </div>
            </div>
            <div style="background-color: black; padding: 2px 10px; color: white; font-weight: bold; width: 85px; position: absolute">
            {{ trans('supplier.destinatario') }}
            </div>
            <div class="second-block">
                <div class="row medium">
                    <div>
                        <span>{{ $supplier_order->order->customer->address->name }}</span> <br>
                        <span>{{ $supplier_order->order->customer->address->address1 }}</span> <br>
                        <span>{{ $supplier_order->order->customer->address->address2 }}</span> <br>
                        <span>{{ $supplier_order->order->customer->address->city }}</span> <br>
                        <span><b style="margin-right: 10px">{{ $supplier_order->order->customer->address->zipcode }}</b> {{ $supplier_order->order->customer->address->province }} / {{ $supplier_order->order->customer->address->province_code }}</span> <br>
                        <div style="width:100%; height:90px;">
                            <?php echo DNS1D::getBarcodeSVG(preg_replace('/\D/', '', $supplier_order->order->customer->address->zipcode), 'UPCA', 2, 90, 0, false); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="third-block">
                <div class="row medium">
                    <div>
                        <span>{{ $supplier_order->order->shop->name }}</span> <br>
                        @if($authenticated_user->use_shipment_address && $authenticated_user->shipment_address && $authenticated_user->shipment_address->street != null)
                            <span>{{ $authenticated_user->shipment_address->street }}, {{ $authenticated_user->shipment_address->number }}</span> <br>
                            <span>{{ $authenticated_user->shipment_address->district }}</span> <br>
                            <span>{{ $authenticated_user->shipment_address->city }} / {{ $authenticated_user->shipment_address->state_code }}</span> <br>
                            <span><b style="margin-right: 10px">{{ $authenticated_user->shipment_address->zipcode }}</b></span> <br>
                        @else
                            <span>{{ $supplier_order->order->shop->address->street }}, {{ $supplier_order->order->shop->address->number }}</span> <br>
                            <span>{{ $supplier_order->order->shop->address->district }}</span> <br>
                            <span>{{ $supplier_order->order->shop->address->city }} / {{ $supplier_order->order->shop->address->state_code }}</span> <br>
                            <span><b style="margin-right: 10px">{{ $supplier_order->order->shop->address->zipcode }}</b></span> <br>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
</body>
</html>

<script type="text/javascript">
    window.print();
</script>
