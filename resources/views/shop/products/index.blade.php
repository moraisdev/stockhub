@extends('shop.layout.default')

@section('title', config('app.name').' - Meus Produtos')

@section('stylesheets')
<style type="text/css">
    .btn-circle {
        padding: 7px 10px;
        border-radius: 50%;
        font-size: 1rem;
    }
</style>
@endsection

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
               
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Produtos</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">
                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#add-private-product-modal">Adicionar produto privado</button>
                            </div>
                            
                             <div class="float-right" style="margin: 0 15px;">
                                <button class="btn btn-sm btn-success"  onclick="ExportAllProductsBling()">Exportar Produto Bling</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">IMAGEM</th>
                                <th scope="col">Título</th>
                                <th scope="col">SKU/EAN GTIN</th>
                                <th scope="col">Qtd</th>
                                <th scope="col" class="actions-th">Visualizar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <th scope="row">
                                @if ($product->img_source != '')   
                                            <img id="img_source_{{ $product->id }}" src="{{$product->img_source}}" class="img-fluid" style="max-height:50px">
                                            @else 
                                            <img id="img_source_{{ $product->id }}" src="{{asset('assets/img/products/eng-product-no-image.png')}}" class="img-fluid" style="max-height:50px">
                                             @endif 
                                </th>
                                <td>
                                    {{ substr($product->title, 0,30)."..." }}
                                </td>
                                  <td>
                                    {{ $product->sku }} <br>
                                    {{ $product->ean_gtin }}<br>   
                                                           
                                </td>
                                 
                               
                                <td>
                                      {{ $product->variants->sum('stock.quantity') }}
                                </td>
                                <td class="actions-td">
                                    <a href="{{ route('shop.products.show', $product->id) }}" class="btn btn-primary btn-circle" role='button' tooltip="true" title="Detalhes do produto"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('shop.products.download', $product->id) }}" class="btn btn-warning btn-circle" role='button' tooltip="true" title="Detalhes do produto"><i class="fas fa-download"></i></a>
                                    @if(!empty($authenticated_user->shopify_app))

                                    <a href="javascript:void(0);" class="{{!$authenticated_user->shopify_app ? 'disabled' : ''}} btn btn-success btn-circle" style='background-color: #017f62 !important; border: #017f62 !important;' role='button' tooltip="true" title="Exportar para o shopify" onClick="exportProductShopifyJson({{$product->id}},'{{$product->title}}')" data-toggle="modal" data-target="#export-shopify-modal">
                                        <img style='height: 20px; margin-top: -5px !important;' src="{{asset('img/icons/icon-shopify.png')}}">
                                    </a>
                                    @endif
                                    @if(!empty($authenticated_user->woocommerce_app))

                                    <a href="{{ route('shop.products.exportWoocommerce', $product->id) }}" class="{{!$authenticated_user->woocommerce_app ? 'disabled' : ''}} btn btn-success btn-circle" style='background-color: #95598c !important; border: #95598c !important;' role='button' tooltip="true" title="Exportar para o Woocommerce">
                                        <img src="{{asset('img/icons/icon-woocommerce.png')}}" style='height: 20px; margin-left: -5px;' alt="" srcset="">
                                    </a>
                                    @endif
                                    @if(!empty($authenticated_user->cartx_app))
                                    <a href="{{ route('shop.products.export-cartx', $product->id) }}" class="{{!$authenticated_user->cartx_app ? 'hidden' : ''}} btn btn-success btn-circle" style='background-color: #2487fe !important; border: #2487fe !important;' role='button' tooltip="true" title="Exportar para o cartx">
                                        <img src="{{asset('img/icons/icon-cartx.png')}}" style='height: 20px; margin-top: -3px !important; margin-left: -3px;' alt="" srcset="">
                                    </a>
                                    @endif
                                    @if(!empty($authenticated_user->yampi_app))
                                    <a href="{{ route('shop.yampi.exp_product', $product->id) }}" class="btn btn-success btn-circle" style='height: 41px; width: 41px; border-radius: 100%;background-color: #FFFFFF !important; border: #352b72 !important;' role='button' tooltip="true" title="Exportar para a Yampi" >
                                        <img src="{{asset('img/icons/icon-yampi.png')}}" style='height: 22px; margin-top: -3px !important; margin-left: 0px;' alt="" srcset="">
                                    </a>
                                    @endif

                                    @if(isset($apimercadolivreapi))
                                    <a href="{{ route('shop.products.export-ml', $product->id) }}" class="btn btn-success btn-circle" style='height: 41px; width: 41px; border-radius: 100%;background-color: #FFFFFF !important; border: #352b72 !important;' role='button' tooltip="true" title="Exportar para a ML">
                                        <img src="{{asset('img/icons/ml.png')}}" style='height: 22px; margin-top: -3px !important; margin-left: 0px;' alt="" srcset="">
                                    </a>
                                    @endif

                                    @if(isset($shop->bling_apikey))
                                    <a href="{{ route('shop.bling.exp_product', $product->id) }}" class="btn btn-success btn-circle" style='height: 41px; width: 41px; border-radius: 100%;background-color: #FFFFFF !important; border: #352b72 !important;' role='button' tooltip="true" title="Exportar para Bling">
                                        <img src="{{asset('img/bling2.png')}}" style='height: 22px; margin-top: -3px !important; margin-left: 0px;' alt="" srcset="">
                                    </a>
                                    @endif
                               
                                    
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                    Nenhum produto ligado à sua loja.
                                </th>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    <div class="float-right">
                        {{ $products->render() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="add-private-product-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('shop.products.link_private') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar produto privado</h5>
                </div>
                <div class="modal-body">
                    <p>Produtos privados estarão disponíveis apenas para aqueles que possuem a hash do produto, disponibilizada pelo fornecedor. Caso você tenha recebido uma hash de um fornecedor, você pode adicionar o produto à sua loja através do campo abaixo.</p>
                    <div class="form-group mb-0">
                        <label>Hash do produto</label>
                        <input type="text" name="hash" class="form-control" placeholder="Hash do produto">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success">Adicionar produto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="export-shopify-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exportando produto</h5>
            </div>
            <div class="modal-body">
                <p id='product_name'>NOME PRODUTO</p>
                <div id="pass_1" class='mb-2'>
                    <img src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}' style='height: 30px;'> Enviando dados para a Shopify...
                </div>
                <div id="pass_2">
                    
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" tabindex="-1" role="dialog" id="exportar-product-bling-modal">
<div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Exportando produtos do Bling</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    <img src="{{ asset('assets/img/Spinner-1s-200px (1).gif') }}" style="height: 30px;" id="imgok" ><a id ="txtstatus">Exportando e Atualizando Produto Bling</a>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    

@endsection

@section('scripts')
<script>
    function exportProductShopifyJson(product_id, product_name){
        $('#product_name').html(product_name)
        $('#pass_1').html("<img src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}' style='height: 30px;'> Enviando dados para a Shopify...")
        $('#pass_2').html("")
        $.ajax({
            url: '{{ route("shop.products.export_shopify_json") }}',
            method: 'GET',
            data: { product_id },
            beforeSend: function(){
            },
            success: function(response){
                $('#pass_1').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> '+response.msg+'</span>')
                exportImagesProductShopifyJson(response.shopify_product, response.product_id)
            },
            error: function(error){
                console.log(error.responseJSON)
                $('#pass_1').html('<span style="color: #f5365c !important;"><i class="fas fa-check-circle"></i> '+error.responseJSON.error+'</span>')
            }
        })
        
        setTimeout(() => {
            $('#pass_1').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> Produto exportado para o Shopify com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.</span>')
        }, 30000)
    }

    function exportImagesProductShopifyJson(shopify_product, product_id){
        //$('#pass_2').html("<img src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}' style='height: 30px;'> Enviando imagens do produto para a Shopify...")
        $.ajax({
            url: '{{ route("shop.products.export_images_shopify_json") }}',
            method: 'POST',
            data: { shopify_product, product_id,  _token: "{{ csrf_token() }}" },
            beforeSend: function(){
            },
            success: function(response){
                console.log(response.msg)
                // $('#pass_2').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> '+response.msg+'</span>')
            },
            error: function(error){
                console.log(error.responseJSON.error)
                //$('#pass_2').html('<span style="color: #f5365c !important;"><i class="fas fa-check-circle"></i> '+error.responseJSON.error+'</span>')
            }
        })
    }

    function exportProductWoocommerceJson(product_id, product_name){
        $('#product_name').html(product_name)
        $('#pass_1').html("<img src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}' style='height: 30px;'> Enviando dados para a Woocommerce...")
        $('#pass_2').html("")
        $.ajax({
            url: '{{ route("shop.products.export_woocommerce_json") }}',
            method: 'GET',
            data: { product_id },
            beforeSend: function(){
            },
            success: function(response){
                $('#pass_1').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> '+response.msg+'</span>')
                exportImagesProductWoocommerceJson(response.woocommerce_product, response.product_id)
            },
            error: function(error){
                console.log(error.responseJSON)
                $('#pass_1').html('<span style="color: #f5365c !important;"><i class="fas fa-check-circle"></i> '+error.responseJSON.error+'</span>')
            }
        })
        
        setTimeout(() => {
            $('#pass_1').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> Produto exportado para o Woocommerce com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.</span>')
        }, 30000)
    }

    function exportImagesProductWoocommerceJson(woocommerce_product, product_id){
        //$('#pass_2').html("<img src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}' style='height: 30px;'> Enviando imagens do produto para a Woocommerce...")
        $.ajax({
            url: '{{ route("shop.products.export_images_woocommerce_json") }}',
            method: 'POST',
            data: { woocommerce_product, product_id,  _token: "{{ csrf_token() }}" },
            beforeSend: function(){
            },
            success: function(response){
                console.log(response.msg)
                // $('#pass_2').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> '+response.msg+'</span>')
            },
            error: function(error){
                console.log(error.responseJSON.error)
                //$('#pass_2').html('<span style="color: #f5365c !important;"><i class="fas fa-check-circle"></i> '+error.responseJSON.error+'</span>')
            }
        })
    }

    function exportProductYampiJson(product_id, product_name){

        $('#product_name').html(product_name)

        $('#pass_1').html("<img src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}' style='height: 30px;'> Enviando dados para a Yampi...")

        $('#pass_2').html("")

        $.ajax({

            url: '{{ route("shop.yampi.export_product") }}',

            method: 'GET',

            data: { product_id },

            beforeSend: function(){

            },

            success: function(response){

                $('#pass_1').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> '+response.success+'</span>')

                exportImagesProductYampiJson(response.yampi_product, response.product_id)

            },

            error: function(error){

                console.log(error.responseJSON)

                $('#pass_1').html('<span style="color: #f5365c !important;"><i class="fas fa-check-circle"></i> '+error.responseJSON.error+'</span>')

            }

        })



        setTimeout(() => {

            $('#pass_1').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> Produto exportado para o Yampi com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.</span>')

        }, 30000)

        }


        function exportImagesProducYampiJson(yampi_product, product_id){

        //$('#pass_2').html("<img src='{{asset('assets/img/Spinner-1s-200px (1).gif')}}' style='height: 30px;'> Enviando imagens do produto para a yampi...")

        $.ajax({

            url: '{{ route("shop.yampi.export_images") }}',

            method: 'POST',

            data: { yampi_product, product_id,  _token: "{{ csrf_token() }}" },

            beforeSend: function(){

            },

            success: function(response){

                console.log(response.msg)

                // $('#pass_2').html('<span style="color: #2dce89 !important;"><i class="fas fa-check-circle"></i> '+response.msg+'</span>')

            },

            error: function(error){

                console.log(error.responseJSON.error)

                //$('#pass_2').html('<span style="color: #f5365c !important;"><i class="fas fa-check-circle"></i> '+error.responseJSON.error+'</span>')

            }

        })

        }
        
   function ExportAllProductsBling() {

            var _token = $('meta[name="_token"]').attr('content');
            var bling = 1;

            $.ajax({
                url:'{{ route("shop.export_bling_json") }}',
                cache: false,
                type:'GET',

                data: {   _token: _token , bling  },
                beforeSend: function() {
                $("#exportar-product-bling-modal").modal('show');
                                        },
                    success:function(response){
					console.log(response)	
                    res = response;
					console.log(res)	
                    if (res == "Exportação OK") {
                    document.querySelector("#txtstatus").innerHTML = "Exportação Concluida" ;
                    document.querySelector("#imgok").src = "{{asset('assets/img/confirm.gif')}}";
                    console.log(res);
                      }},
      error: function(response) {
            document.querySelector("#txtstatus").innerHTML = "Erro na Importação tente mais Tarde" ;
            document.querySelector("#imgok").src = "{{asset('assets/img/erro.gif')}}";
                        }
                    });

    }       
        
        
</script>
    
@endsection
