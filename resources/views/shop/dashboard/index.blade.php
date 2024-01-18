@extends('shop.layout.default')

@section('title', config('app.name').' - Dashboard')

@section('content')
<style>
    .slide-reponsive-desktop{
        display: none !important;
    }

    .slide-reponsive-mobile{
        display: block  !important;
    }

    @media(min-width: 1000px){
        .slide-reponsive-desktop{
            display: block  !important;
        }

        .slide-reponsive-mobile{
            display: none !important;
        }
    }

    .img-mobile{
        display: none;
    }

    @media(max-width: 767px){
        .img-mobile{
            display: block;
        }

        .img-desktop{
            display: none;
        }
    }
    
</style>
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
             <div id="carouselHome" class="carousel slide carousel-home-custom mb-4" data-ride="carousel" data-interval='5000'>
                <div class="carousel-inner">
                @if(isset($banners[0]))
                  <div class="carousel-item active">
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSfS7APOzZ-5mmomnbPrBoFqFwYicWj--z1G2d3z7AMQn2CCdA/viewform" target='_blank'>
                        <img class="d-block w-100 slide-reponsive-desktop" src="{{$banners[0]->img_source}}">
                        <img class="d-block w-100 slide-reponsive-mobile" src="{{$banners[0]->img_source_mobile}}">
                    </a>
                  </div>
                  @endif
                  @if(isset($banners[1]))
                  <div class="carousel-item">
                    <a href="#" target='_blank'>
                        <img class="d-block w-100 slide-reponsive-desktop" src="{{asset('assets/static/banners/03banner_desktop_1953x228px.jpg')}}">
                        <img class="d-block w-100 slide-reponsive-mobile" src="{{asset('assets/static/banners/03banner_mobile_800x800px.jpg')}}">
                    </a>
                  </div>
                  @endif
                  @if(isset($banners[2]))
                  <div class="carousel-item">
                    <a href="https://api.whatsapp.com/send?phone=5511987155948&text=Ol%C3%A1%2C%20conheci%20a%20PJ%20Rocks%20pela%20Mawa%20Post%20e%20gostaria%20de%20obter%20mais%20informa%C3%A7%C3%B5es%20por%20favor" target='_blank'>
                        <img class="d-block w-100 slide-reponsive-desktop" src="{{asset('assets/static/banners/banner_desktop_1953x228px.jpg')}}" >
                        <img class="d-block w-100 slide-reponsive-mobile" src="{{asset('assets/static/banners/banner_mobile_800x800px.jpg')}}">
                    </a>
                  </div>
                
                @endif
                </div>
                <a class="carousel-control-prev" href="#carouselHome" role="button" data-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselHome" role="button" data-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
            </div> 
            <!-- Card stats -->
            
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total pendente</h5>
                                    <span class="h2 font-weight-bold mb-0">R$ {{ number_format($dashboard_data['total_pending'],2,',','.') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total pago aos fornecedores</h5>
                                    <span class="h2 font-weight-bold mb-0">R$ {{ number_format($dashboard_data['total_cost'],2,',','.') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">

                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total Pedidos</h5>
                                    <span class="h2 font-weight-bold mb-0">R$ {{ number_format($dashboard_data['total_earning'],2,',','.') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--7">
    <div class="row">
        @if(!$authenticated_user->address || !$authenticated_user->document)
            <div class="col-12 mb-3">
                <div class="card shadow bg-danger">
                    <div class="card-body text-white py-3">
                        Você precisa terminar de preencher os dados de sua conta para vender através do {{config('app.name')}}. Clique <a href="{{ route('shop.profile') }}">aqui</a> para atualizar seus dados.
                    </div>
                </div>
            </div>
        @endif
        @if($authenticated_user->status == 'inactive')
            <div class="col-12 mb-2">
                <div class="card shadow bg-danger">
                    <div class="card-body text-white py-2">
                        O pagamento de sua assinatura está pendente e o seu acesso ao painel e às funções do {{config('app.name')}} estão limitados. Em caso de dúvidas, entre em contato com nosso suporte clicando <a href="https://api.whatsapp.com/send?phone={{ env('SUPPORT_WHATSAPP') }}&text=Ola%2C%20vim%20do%20SAC%20e%20gostaria%20de%20falar%20com%20a%20equipe%20de%20assist%C3%AAncia%20a%20Lojistas" target="_blank">aqui</a>.
                    </div>
                </div>
            </div>
        @endif
        
    	<div class="col-12 mb-3">
    		<div class="card shadow">
    			<div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h6 class="text-uppercase text-muted ls-1 mb-1">Seus últimos 10 pedidos</h6>
                            <h2 class="mb-0">Pedidos recentes</h2>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="float-right">
                                <a href="{{ route('shop.orders.index') }}" class="btn btn-info">Ver todos pedidos</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Ref. Externa/Valor </th>
                            <th>Cliente</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->name }}</td>
                                <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                @if($order->external_service == 'shopify')
                                    <td>{{ ucfirst($order->external_service) }}: <br><a href="https://{{ $authenticated_user->shopify_app->domain }}.myshopify.com/admin/orders/{{ $order->external_id }}" target="_blank">#{{ $order->external_id }}</a> <br>
                                      
                               
                                @endif
                                @if($order->external_service == 'cartx')
                                    <td>{{ ucfirst($order->external_service) }}: <br> <a href="https://accounts.cartx.io/orders/details/{{ $order->external_id }}" target="_blank">#{{ $order->external_id }}</a></td>
                                @endif
                                </td>
                                <td>R$ {{ number_format($order->amount, 2, ',', '.') }} <br> ({{ ucfirst($order->external_service) }}: R$ {{ number_format($order->external_price, 2, ',', '.') }})</td>
                                <td class="text-gray">
                                    - {{ ($order->customer) ? $order->customer->first_name.' '.$order->customer->last_name : '???' }}<br>
                                    - {{ ($order->customer) ? $order->customer->email : '???' }}<br>
                                    - {{ ($order->customer) ? $order->customer->address->phone : '???' }}<br>
                                    - {{ ($order->customer) ? $order->customer->address->address1.', '.$order->customer->address->address2.'-'.$order->customer->address->city.'/'.$order->customer->address->privince_code.'-'.$order->customer->address->zipcode : '???' }}
                                </td>
                                <td>
                                    <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes">
                                        <i class="fas fa-fw fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Nenhum pedido pendente de pagamento.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
    		</div>
    	</div>
    </div>
</div>

{{-- <div class="modal fade" role="dialog" tabindex="-1" id="promocao-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header mt-0">
                <h5 class="modal-title">PROMOÇÃO ESPECIAL</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-0">
                <a href="https://painel.mawapost.com/shop/products/details/8626f7fe3a537187cdc2d98162fafa56" target="_blank">
                    <img class="w-100 img-mobile" src="{{asset('assets/static/banners/07banner_mobile_800x800px.jpg')}}">
                    <img class="w-100 img-desktop" src="{{asset('assets/static/banners/banner_752x496px.jpg')}}">
                </a>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@section('scripts')
    <script>
        // $(document).ready(function(){
        //     $('#promocao-modal').modal('show')
        // })
    </script>
@endsection
