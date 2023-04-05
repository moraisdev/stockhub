<!DOCTYPE html>
<html>
<head>
	<title>{{config('app.name')}} - {{ trans('supplier.tag_pedido') }} #{{ $supplier_order->display_id }}</title>

	<style type="text/css">
		body{
			margin:0;
			padding:10px;
		}

        .tag{
            width: 378px;
            height: 566px;
            
            box-sizing: border-box;
        }

        .first-block{
            box-sizing: border-box;
            width: 100%;
            height: 350px;           
            padding: 5px;
        }

		.second-block{
			box-sizing: border-box;
			width: 100%;
			height: 190px;
			border-bottom: 1px solid black;
			padding: 25px;
            margin-top : -215px;
		}

		.third-block{
			box-sizing: border-box;
			width: 100%;
			height: 100px;
			padding: 10px 25px;
			margin-top: -10px;
		}

		.tag-header{
			height: 110px;
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
			align-self: center;
			display: flex;
			margin-bottom: -80px;
			text-align: center;
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
        .quadrado {
            background: #d3d3d3;
            width: 100px;
            height: 50px;
            display: flex;
			align-self: center;
        }
        .imgcorreios{
			display: flex;
			width: 50px;
			height: 50px;
			text-align: right;
		}
        .tag-destinatario{
			height: 10px;
			display: flex;
			justify-content: space-between;
			border-top: 1px solid black;
			
			
		}
        .dest-block-1{
			display: flex;
			width: 115px;
			height: 10px;
			align-self: center;
			
		}

		.dest-block-2{
			width: 115px;
			height: 10px;
		}

		.dest-block-3{
			display: flex;
			width: 115px;
			height: 10px;
			align-self: center;
		}
	</style>
</head>
<body>
	<div class="tag">
		<div class="first-block">
            <div class="tag-header">
                <div class="quadrado">
                <span style="display: block; margin-bottom: 10px;"> {{ $supplier_order->order->shop->name  }} </span>
                </div>
                <div class="header-block-2" style= "justify-content: center;">
                <?php
                
                $cepdestino = preg_replace('/\D/', '', $supplier_order->order->customer->address->zipcode);
                $ceporigem = preg_replace('/\D/', '', $supplier_order->order->shop->address->zipcode);
                $dados = $cepdestino + $ceporigem;

                $cepClienteArray = str_split($cepdestino);
                $somaCep = 0;
                foreach ($cepClienteArray as $pos){
                $somaCep += $pos;
                }
                $multiplo = ceil($somaCep/ 10) * 10;
                $validador = $multiplo - $somaCep;
                
               
                  echo DNS2D::getBarcodeSVG("$cepdestino"."00000".$ceporigem."00000".$validador."51".$shipping->tracking_number."250000000000007533630803298000016800000081996366731-00.000000-00.000000|", 'DATAMATRIX' , 2.0 , 2.0); ?> 
                </div>
                <div class="header-block-3">
                    
                </div>
            </div>
            @php
                $order_id = str_pad($supplier_order->display_id, 8, "0", STR_PAD_LEFT);
            @endphp
			<div class="row" style="margin-top: 5px">
				<div class="w-100 small">
					{{--Nota fiscal: 12345678910<br>--}}
					{{ trans('supplier.pedido') }}: #{{ $order_id }} <br>
					PLP: 
				</div>
				<div class="w-160 small">
					 Contrato: {{ $order_id }}<br>
                      {{ $shipping->company }}<br>
				</div>
				<div class="w-100 small">
					Volume 1/1
                   
				</div>
			</div>
			<div class="row" style="margin-top: 5px">
				<div class="full-col align-left" style="margin-left: 5px; margin-right: 25px; margin-top: 10px; height: 95px; width:10px ">
					<b style="display:block; text-align: center; font-size:20px;">{{ $shipping->tracking_number }}</b>
					<?php echo DNS1D::getBarcodeSVG("$shipping->tracking_number", 'C128', 2, 75, 0, false); ?>
				</div>
			</div>
			<div class="row" style="margin-top: 20px">
				<div class="small">
                    <span style="display: block; margin-bottom: 10px;">{{ trans('supplier.recebedor') }}: _________________________________________________________________</span>
                    <span style="display: block">{{ trans('supplier.assinatura_title') }}: ______________________________ {{ trans('supplier.documento') }}: ______________</span>
				</div>
			</div>
		</div>
       
		<div class="tag-destinatario">
                <div class="dest-block-2">
                <span style="display: block; margin-bottom: 10px; background-color: black; padding: 2px 10px; color: white; font-weight: bold; width: 85px; position: absolute"> {{ trans('supplier.destinatario') }} </span>
                </div>
                <div class="dest-block-2">
               
                </div>
                <div class="dest-block-3">
                    <img src="{{ asset('assets/img/correios.png') }}" style="max-width:100px; max-height: 100px; align-self: center; margin-bottom: -12px;">
                </div>
            </div>

        </div> 

        <div class="second-block">
			<div class="row medium">
				<div>
					<span>{{ $supplier_order->order->customer->address->name }}</span> <br>
					<span>{{ $supplier_order->order->customer->address->address1 }}, {{ $supplier_order->order->customer->address->number }}  </span> <br>
					<span>{{ $supplier_order->order->customer->address->complement}},{{ $supplier_order->order->customer->address->address2 }}</span> <br>
					<span>{{ $supplier_order->order->customer->address->city }}</span> <br>
					<span><b style="margin-right: 10px">{{ $supplier_order->order->customer->address->zipcode }}</b> {{ $supplier_order->order->customer->address->province }} / {{ $supplier_order->order->customer->address->province_code }}</span> <br>
					<div style="width:100%; height:90px;">
						<?php echo DNS1D::getBarcodeSVG(preg_replace('/\D/', '', $supplier_order->order->customer->address->zipcode), 'C128', 2, 75, 0, false); ?>
					</div>
				</div>
			</div>
		</div>

       
		<div class="third-block">
			<div class="row medium">
                <div>
                {{ trans('supplier.remetente') }}:
                    <span>{{ $supplier_order->order->shop->name }}</span> <br>
                    @if($authenticated_user->use_shipment_address && $authenticated_user->shipment_address && $authenticated_user->shipment_address->street != null)
                        <span>{{ $authenticated_user->shipment_address->street }}, {{ $authenticated_user->shipment_address->number }}</span> <br>
                        <span>{{ $authenticated_user->shipment_address->district }}</span> <br>
                        <span><b style="margin-right: 10px"> {{ $supplier_order->order->shop->address->zipcode }} </b>  {{ $authenticated_user->shipment_address->city }} / {{ $authenticated_user->shipment_address->state_code }}</span> <br>
                        <span><b style="margin-right: 10px">{{ $authenticated_user->shipment_address->zipcode }}</b></span> <br>
                    @else
                        <span>{{ $supplier_order->order->shop->address->street }}, {{ $supplier_order->order->shop->address->number }}</span> <br>
                        <span>{{ $supplier_order->order->shop->address->district }}</span> <br>
                        <span><b style="margin-right: 10px">{{ $supplier_order->order->shop->address->zipcode }} </b>  {{ $supplier_order->order->shop->address->city }} / {{ $supplier_order->order->shop->address->state_code }}</span> <br>
                        <br>
                    @endif
                </div>
			</div>
		</div>
	</div>

  <div class = "container" style="width: 378px;">
          
	<h2 style="text-align: right;" > 	<?php echo DNS1D::getBarcodeSVG("$order_id", 'C128', 1, 45, 0, false); ?>  </h2>
	<h3 style="text-align: center; text-size=15px;">{{ trans('supplier.declaracao_conteudo') }}</h3>
		 

 
          <h3 style="text-align: center;">{{ trans('supplier.numero_pedido') }}: {{$order_id}} </h3>
          <div class="medium">
		  <h4 style="text-align: left;">{{ trans('supplier.remetente') }}</h4>
		  
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
		  <h4 style="text-align: left;">{{ trans('supplier.destinatario') }}</h4>
		  <div class="medium">
		  <span>{{ $supplier_order->order->customer->address->name }}</span> <br>
					<span>{{ $supplier_order->order->customer->address->address1 }},{{ $supplier_order->order->customer->address->number }} </span> <br>
					<span>{{ $supplier_order->order->customer->address->address2 }}</span> <br>
					<span>{{ $supplier_order->order->customer->address->city }}</span> <br>
					<span><b style="margin-right: 10px">{{ $supplier_order->order->customer->address->zipcode }}</b> {{ $supplier_order->order->customer->address->province }} / {{ $supplier_order->order->customer->address->province_code }}</span> <br>
			</div>
          <h4> Itens do Pedido </h4>
         
          <div class="card-body">
                <table class="table" >
                  <thead >
                    <tr >
                      <th >{{ trans('supplier.sku') }}</th>
                      <th >{{ trans('supplier.product') }}</th>
                      <th>{{ trans('supplier.quantidade') }}</th>                    
                    </tr>
                  </thead>
                  <tbody >
				  @forelse($supplier_order->items as $item)
                  @if($item->variant)
                    <tr>
                    <td >{{ $item->variant->sku  }}</td>
                      <td >{{ $item->variant->title }}</td>
                      <td > {{ $item->quantity}}</td>
                      </tr>
					  @endif
                     @empty
                    @endforelse

                  </tbody>
                </table>



				



            </div>












</body>
</html>





<script type="text/javascript">
	window.print();
</script>
