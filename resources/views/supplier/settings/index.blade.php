@extends('supplier.layout.default')

@section('title', __('supplier.configuracoes_title'))

@section('stylesheets')
<style type="text/css">
    .btn-circle {
        padding: 7px 10px;
        border-radius: 50%;
        font-size: 1rem;
    }

    input:checked+.toggle-slider-success{
      border: 1px solid #2dce89 !important;
    }

    input:checked+.toggle-slider-success:before{
      background: #2dce89 !important;
    }
</style>
@endsection

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row mb-4">
        <div class="col-md-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ trans('supplier.plano') }} {{config('app.name')}}</h3>                                
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Caso não seja o plano gratuito --}}
                            @if($planoSupplier && $authenticated_user->contracted_plan->plan_id != 999) 
                            <div class="form-group">
                                <h3>{{$planoSupplier->Plan->Name}}</h3>
                                <p>
                                    Situação: <b>{{$planoSupplier->SubscriptionStatus->Name}}</b><br>
                                    {{ trans('supplier.price') }}: <b>R$ {{ number_format($planoSupplier->Plan->Amount, 2,',','.')}} / {{$planoSupplier->Plan->PlanFrequence->Name}}</b>
                                    <br>
                                    Método de pagamento: <b>{{$planoSupplier->PaymentMethod->Name}}</b>
                                    <br>
                                    Vencimento: <b>{{$vencimentoPlano}}</b>
                                </p>
                                {{-- Só exibe caso tenha algum cancelamento e a data ainda não tenha chegado --}}
                                @if($authenticated_user->canceled_plan && $authenticated_user->canceled_plan->status == 'pending' && strtotime(date('Y-m-d')) <= strtotime($authenticated_user->canceled_plan->change_date))
                                    <small>* Há um cancelamento agendado para a data <b>{{date("d/m/Y", strtotime($authenticated_user->canceled_plan->change_date))}}</b>, a partir dessa data a sua conta voltará para o plano <b>{{ trans('supplier.gratuito') }}</b></small>
                                @endif                                
                            </div>
                            <div class="form-group text-center">   
                                <a href='{{route('supplier.plans.cancel')}}' style="color: #32325d; font-size: 9pt;">Cancelar Plano</a>
                            </div>                         
                            @else
                                {{-- Caso seja o plano gratuito --}}
                                <div class="form-group">
                                    <h3>Plano Gratuito</h3>
                                    <p>
                                    {{ trans('supplier.situacao') }}: <b>{{ trans('supplier.ativa') }}</b><br>
                                    </p>
                                    {{-- Mensagens do plano gratuito --}}
                                    @if($stringPlanoGratuito != '')
                                        <p>{{$stringPlanoGratuito}}</p>
                                    @endif
                                </div>
                                {{-- <div class="form-group text-center">                            
                                    <a href='{{route('supplier.plans.index')}}' class="btn btn-primary mb-3" style="color: #fff; margin-right: 0;">Alterar Plano</a>
                                </div> --}}
                            @endif
                        </div>
                        {{-- <div class="col-md-6">
                            <h4>Valor processado nesse mês</h4>
                            <p>R$ {{$monthBilling}}</p>
                            <h3>
                                Próxima mensalidade<br>
                                <b> R$ {{$nexPlan}}</b>
                            </h3>
                            @if($nexPlan)
                                <button class='btn btn-success' onclick="window.location='{{route('supplier.plans.selected', ['plan_id' => 7763])}}'">Pagar agora</button>
                            @endif
                        </div> --}}
                    </div>
                        
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-12 mb-5 mb-xl-0">
            <div class="card shadow mb-4">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Configurações Bling</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supplier.settings.update_bling') }}">
                        @method('PUT')
                        @csrf
                        
                        @if($authenticated_user->bling_apikey != '')
                            <div class="row">                            
                                <div class="col-lg-12 col-12">
                                    <div class="form-group">
                                        <label>Gerar código de rastreio automático ao enviar pedido pro bling</label>
                                        <select name="bling_automatic_tracking_code" id="bling_automatic_tracking_code" class="form-control">
                                            <option value="false" {{ $authenticated_user->bling_automatic_tracking_code == 'false' ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                            <option value="true" {{ $authenticated_user->bling_automatic_tracking_code == 'true' ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                        </select>
                                    </div>
                                    <div class='form-group'>
                                        <p>Aceitar pedidos mesmo com estoque zerado?</p>
                                        <div class="custom-control custom-radio mb-3">
                                            <input type="radio" id="radio_stock1" name="radio_stock" class="custom-control-input" {{ $authenticated_user->empty_stock_bling == 'sim' ? 'checked' : '' }} value='sim'>
                                            <label class="custom-control-label" for="radio_stock1">{{ trans('supplier.sim') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="radio_stock2" name="radio_stock" class="custom-control-input"  {{ $authenticated_user->empty_stock_bling == 'nao' ? 'checked' : '' }} value='nao'>
                                            <label class="custom-control-label" for="radio_stock2">{{ trans('supplier.nao') }}</label>
                                        </div>
                                    </div>
                                </div>
                                
                                  
                            </div>
                        @endif        
                        <div class="row">                            
                            <div class="col-lg-9 col-12">
                                <div class="form-group">
                                    <label>Chave da API</label>
                                    <div class="input-group">
                                       <input type="text" name="bling_apikey" placeholder="Insira aqui o código da API no Bling" class="form-control" value="{{ $authenticated_user->bling_apikey ? $authenticated_user->bling_apikey : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="form-group">
                                    <label class="d-block">&nbsp;</label>
                                    <button class="btn btn-success btn-block pull-right"><i class="fas fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <label class="d-block">Você pode adicionar a configuração de CallBack de alteração de Estoque Bling. sua URL: {{env('APP_URL')}}/api/bling/update-stock
</label>

                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Tipo de Frete</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>Escolha o método de cálculo do frete que deseja utilizar.</p>
                    <div class="form-group mb-0">
                        <select name="shipping_method" id="shipping_method" class="form-control" onchange="updateShippingType()" required>
                            <option value="no_shipping" {{ $authenticated_user->shipping_method == 'no_shipping' ? 'selected' : '' }}>Não cobrar frete</option>
                            <option value="melhor_envio" {{ $authenticated_user->shipping_method == 'melhor_envio' ? 'selected' : '' }}>Melhor Envio</option>
                        </select>
                    </div>
                    {{-- Mostrar dados do melhor envio depois de redirecionar --}}
                    @if(request()->query('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text">{{request()->query('success')}}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(request()->query('error'))
                        <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                            <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                            <span class="alert-text">{{request()->query('error')}}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    
                </div>
            </div>
            <div class="card shadow" id="correios_div" style="display:none">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Tipo de Serviço de Entrega</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('supplier.settings.update_correios') }}">
                    @method('PUT')
                    @csrf
                    <div class="card-body pt-0">
                        <p>Escolha o tipo de serviço utilizado pelos correios para entregar suas encomendas.</p>
                        <div class="form-group mb-0">
                            <select name="correios_services_bling" id="correios_services_bling" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="CORREIOS MINI ENVIOS CTR TA" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CORREIOS MINI ENVIOS CTR TA" ? 'selected' : NULL }}>CORREIOS MINI ENVIOS CTR TA (04235)</option>
                                <option value="SEDEX 12 A VISTA" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "SEDEX 12 A VISTA" ? 'selected' : NULL }}>SEDEX 12 A VISTA (04782)</option>
                                <option value="SEDEX 10 A VISTA" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "SEDEX 10 A VISTA" ? 'selected' : NULL }}>SEDEX 10 A VISTA (04790)</option>
                                <option value="SEDEX HOJE A VISTA" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "SEDEX HOJE A VISTA" ? 'selected' : NULL }}>SEDEX HOJE A VISTA (04804)</option>
                                <option value="SEDEX CONTRATO GDES FORMATOS T" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "SEDEX CONTRATO GDES FORMATOS T" ? 'selected' : NULL }}>SEDEX CONTRATO GDES FORMATOS T (03042)</option>
                                <option value="SEDEX CONTRATO AG TA" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "SEDEX CONTRATO AG TA" ? 'selected' : NULL }}>SEDEX CONTRATO AG TA (03050)</option>
                                <option value="PAC CONTRATO AG TA" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "PAC CONTRATO AG TA" ? 'selected' : NULL }}>PAC CONTRATO AG TA (03085)</option>
                                <option value="PAC CONTRATO GDES FORMATOS TA" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "PAC CONTRATO GDES FORMATOS TA" ? 'selected' : NULL }}>PAC CONTRATO GDES FORMATOS TA (03107)</option>
                                <option value="CARTA SIMPLES SELO E SE PCTE" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CARTA SIMPLES SELO E SE PCTE" ? 'selected' : NULL }}>CARTA SIMPLES SELO E SE PCTE (80152)</option>
                                <option value="CARTA SIMPLES CHANCELA PCTE" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CARTA SIMPLES CHANCELA PCTE" ? 'selected' : NULL }}>CARTA SIMPLES CHANCELA PCTE (80160)</option>
                                <option value="CARTA RG B1 CHANC ETIQUETA" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CARTA RG B1 CHANC ETIQUETA" ? 'selected' : NULL }}>CARTA RG B1 CHANC ETIQUETA (80250)</option>
                                <option value="CARTA REG B1 SELO E SE" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CARTA REG B1 SELO E SE" ? 'selected' : NULL }}>CARTA REG B1 SELO E SE (80276)</option>
                                <option value="CARTA RG AR CONV B1 CHAN ETIQ" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CARTA RG AR CONV B1 CHAN ETIQ" ? 'selected' : NULL }}>CARTA RG AR CONV B1 CHAN ETIQ (80284)</option>
                                <option value="CARTA REG AR CONV B1 SELO SE" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CARTA REG AR CONV B1 SELO SE" ? 'selected' : NULL }}>CARTA REG AR CONV B1 SELO SE (80292)</option>
                                <option value="CARTA RG AR ELTR B1 CHANC ETIQ" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CARTA RG AR ELTR B1 CHANC ETIQ" ? 'selected' : NULL }}>CARTA RG AR ELTR B1 CHANC ETIQ (80900)</option>
                                <option value="CARTA REG AR ELET B1 SELO E SE" {{($authenticated_user->correios_settings) && $authenticated_user->correios_settings->correios_services_bling ==  "CARTA REG AR ELET B1 SELO E SE" ? 'selected' : NULL }}>CARTA REG AR ELET B1 SELO E SE (80080)</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Configurar porcentagem do frete dos correios</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">

                            <p>Você pode selecionar a porcentagem do <span class="text-warning">valor de frete nos correios</span> para definir o quanto será cobrado de seus clientes por pedido.</p>
                            <p>A porcentagem selecionada no campo abaixo é referente ao cálculo de frete nos correios utilizado pela {{config('app.name')}}. Por exemplo, se você selecionar 100% do valor do frete, será cobrado do lojista o mesmo valor de frete buscado na API dos correios.</p>
                            <div class="row">
                                <div class="col-lg-9 col-12">
                                    <div class="form-group">
                                        <label>Porcentagem dos correios</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            </div>
                                            <input type="number" name="percentage" id="percentage" placeholder="Digite a porcentagem do valor de frete dos correios desejada" class="form-control" min="0" max="300" value="{{ ($authenticated_user->correios_settings) ? $authenticated_user->correios_settings->percentage : '0' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="form-group">
                                        <label class="d-block">&nbsp;</label>
                                        <button class="btn btn-success btn-block pull-right"><i class="fas fa-check"></i></button>
                                    </div>
                                </div>
                            </div>

                    </div>
                </form>
                <hr class="my-0">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Simulação do valor de frete</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <p>Efetue abaixo uma simulação de frete nos correios. Essa simulação é útil para definir a porcentagem dos correios que você deseja cobrar de seus lojistas. Por exemplo: caso a simulação de um frete seja R$ 30,00 e você deseja cobrar R$ 10,00 dos lojistas, utilize a porcentagem de 33%.</p>
                    <form action="">
                        <div class="row">
                            <div class="col-lg-5 col-12">
                                <div class="form-group">
                                    <label>CEP origem</label>
                                    <input type="text" name="from_zipcode" placeholder="Digite o CEP de origem" class="form-control cep">
                                </div>
                            </div>
                            <div class="col-lg-2 d-lg-flex d-none align-items-center justify-content-center">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="col-lg-5 col-12">
                                <div class="form-group">
                                    <label>CEP destino</label>
                                    <input type="text" name="to_zipcode" placeholder="Digite o CEP de destino" class="form-control cep">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('supplier.product') }}</label>
                            <select name="product_id" class="form-control">
                                @forelse($variants as $v)
                                    <option value="{{ $v->id }}">{{ $v->title }}</option>
                                @empty
                                    <option value="">Nenhum produto cadastrado</option>
                                @endforelse
                            </select>
                        </div>
                        <button type="button" class="btn btn-warning btn-block" onclick="getShippingPrice()">Simular frete</button>
                        <div class="text-center mt-4">
                            <p class="text-warning mb-0" style="font-size:1.1rem"><b>Frete simulado: <span id="pac_price">R$ 0,00</span></b></p>
                            {{--<p class="text-warning mb-0" style="font-size:1.1rem"><b>SEDEX simulado: <span id="sedex_price">R$ 0,00</span></b></p>--}}
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow" id="total_express_div" style="display:none">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Tipo de serviço Total Express</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <p>Preencha o usuário, senha e tipo de serviço que deseja utilizar para o cálculo de frete da Total Express. O usuário e senha são disponibilizados no painel ICS da Total Express.</p>
                    <form method="POST" action="{{ route('supplier.settings.update_total_express') }}">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Usuário</label>
                                    <input type="text" name="login" class="form-control" placeholder="Usuário de produção" value="{{ $authenticated_user->total_express_settings ? $authenticated_user->total_express_settings->login : '' }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ trans('supplier.text_password') }}</label>
                                    <input type="password" name="password" class="form-control" placeholder="Senha de produção" value="{{ $authenticated_user->total_express_settings ? $authenticated_user->total_express_settings->password : '' }}">
                                </div>
                            </div>
                            <div class="col-lg-9 col-12">
                                <div class="form-group">
                                    <label>Tipo de serviço</label>
                                    <select name="type" class="form-control">
                                        <option value="EXP" {{ $authenticated_user->total_express_settings && $authenticated_user->total_express_settings->type == 'EXP' ? 'selected' : '' }}>Expresso</option>
                                        <option value="ESP" {{ $authenticated_user->total_express_settings && $authenticated_user->total_express_settings->type == 'ESP' ? 'selected' : '' }}>Especial</option>
                                        <option value="PRM" {{ $authenticated_user->total_express_settings && $authenticated_user->total_express_settings->type == 'PRM' ? 'selected' : '' }}>Premium</option>
                                        <option value="STD" {{ $authenticated_user->total_express_settings && $authenticated_user->total_express_settings->type == 'STD' ? 'selected' : '' }}>Standard</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="form-group">
                                    <label class="d-block">&nbsp;</label>
                                    <button class="btn btn-success btn-block pull-right"><i class="fas fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow" id="melhor_envio_div" style="display:none">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Tipo de serviço API Melhor Envio</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    
                    <p>Usar Melhor Envio como base de cálculo para os seus fretes. Lembrando que é somente uma estimativa, a etiqueta tem que ser impressa manualmente pelo fornecedor.</p>
                    <form method="POST" action="{{ route('supplier.settings.update_melhor_envio') }}">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            {{-- <div class="col-12">
                                <div class="form-group">
                                    <label>Client ID</label>
                                    <input type="text" name="client_id" class="form-control" placeholder="Client ID" value="{{ $authenticated_user->melhor_envio_settings ? $authenticated_user->melhor_envio_settings->client_id : '' }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Secret</label>
                                    <input type="text" name="secret" class="form-control" placeholder="Secret" value="{{ $authenticated_user->melhor_envio_settings ? $authenticated_user->melhor_envio_settings->secret : '' }}" required>
                                </div>
                            </div> --}}
                            <div class="col-lg-3 col-12">
                                <div class="form-group text-center">
                                    <label class="d-block">&nbsp;</label>
                                    <button class="btn btn-success pull-right mb-3"><i class="fas fa-check"></i> Usar Melhor Envio</button>
                                    {{-- <small><a href="{{route('supplier.settings.melhor_envio.remove')}}">Remover autorização</a></small> --}}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow" id="no_shipping_div" style="display:none">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Não cobrar frete</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <p>Não será cobrado nenhuma taxa de frete de seus clientes.</p>
                    <form method="POST" action="{{ route('supplier.settings.update_no_shipping') }}">
                        @method('PUT')
                        @csrf
                        <div class="form-group mb-0 float-right">
                            <button class="btn btn-success pull-right"><i class="fas fa-check"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Taxa de manuseio sobre o frete</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form method="POST" action="{{ route('supplier.settings.update_shipping_fee') }}">
                        @method('PUT')
                        @csrf
                        <p>Você pode adicionar uma taxa de manuseio fixa sobre o valor do frete para os seus parceiros. A taxa será inclusa no valor do frete assim que um pedido com seus produtos seja importado para o lojista.</p>
                        <div class="row">
                            <div class="col-lg-9 col-12">
                                <div class="form-group">
                                    <label>Taxa de manuseio</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input type="text" name="shipping_fixed_fee" placeholder="Digite o valor da taxa de manuseio sobre o frete" class="form-control decimal" value="{{ ($authenticated_user->shipping_fixed_fee) ? $authenticated_user->shipping_fixed_fee : '0.00' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="form-group">
                                    <label class="d-block">&nbsp;</label>
                                    <button class="btn btn-success btn-block pull-right"><i class="fas fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            


            

            @if (env('CORREIOS') == 1)
            <div class="card shadow mt-4">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Configurações do contrato com os Correios</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form method="POST" action="{{ route('supplier.settings.correios_contract') }}">
                        @method('PUT')
                        @csrf
                        <p>Caso você possua um contrato com os correios, adicione as informações abaixo para ativar os valores personalizados do seu contrato na hora de cobrar o frete.</p>
{{--                        <p><span class="badge badge-danger">ATENÇÃO!</span> O campo <i>Código Administrativo</i> é opcional, se você não informá-lo o sistema buscará automaticamente na API dos correios.</p>--}}
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Ativar contrato?</label>
                                    <select name="active" id="active" class="form-control">
                                        <option value="0" {{ old('active') && old('active') == 0 ? 'selected' : ($contract && $contract->active == 0 ? 'selected' : '') }}>{{ trans('supplier.nao') }}</option>
                                        <option value="1" {{ old('active') && old('active') == 1 ? 'selected' : ($contract && $contract->active == 1 ? 'selected' : '') }}>{{ trans('supplier.sim') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>Código administrativo</label>
                                    <input type="text" name="sigep_user" placeholder="Usuário SIGEP WEB" class="form-control" value="{{ old('sigep_user') ? old('sigep_user') : ($contract && $contract->sigep_user ? $contract->sigep_user : '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>{{ trans('supplier.text_password') }}</label>
                                    <input type="text" name="sigep_password" placeholder="{{ trans('supplier.text_password') }} SIGEP WEB" class="form-control" value="{{ old('sigep_password') ? old('sigep_password') : ($contract && $contract->sigep_password ? $contract->sigep_password : '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>Código do serviço</label>
                                    <input type="text" name="service_code" placeholder="Código do serviço" class="form-control" value="{{ old('service_code') ? old('service_code') : ($contract && $contract->service_code ? $contract->service_code : '') }}">
                                </div>
                            </div>
{{--                            <div class="col-lg-6 col-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>ID do Contrato</label>--}}
{{--                                    <input type="text" name="contract_id" placeholder="ID do Contrato" class="form-control" value="{{ old('contract_id') ? old('contract_id') : ($contract && $contract->contract_id ? $contract->contract_id : '') }}">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-lg-6 col-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>ID do Cartão de Postagem</label>--}}
{{--                                    <input type="text" name="post_card_id" placeholder="ID do Cartão de Postagem" class="form-control" value="{{ old('post_card_id') ? old('post_card_id') : ($contract && $contract->post_card_id ? $contract->post_card_id : '') }}">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-lg-6 col-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>Código administrativo</label>--}}
{{--                                    <input type="text" name="administrative_code" placeholder="Preenchido automaticamente" class="form-control" value="{{ old('administrative_code') ? old('administrative_code') : ($contract && $contract->administrative_code ? $contract->administrative_code : '') }}">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            @if($contract)--}}
{{--                                <div class="col-lg-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="">Código do serviço <small>(disponíveis em seu contrato)</small></label>--}}
{{--                                        <select name="service_code" class="form-control">--}}
{{--                                            @if($contract->service_code)--}}
{{--                                                <option value="{{ $contract->service_code }}" selected>{{ $contract->service_code }}</option>--}}
{{--                                            @else--}}
{{--                                                <option value="">Selecione o serviço</option>--}}
{{--                                            @endif--}}
{{--                                            @if($contract->services && count(json_decode($contract->services)) > 0)--}}
{{--                                                @foreach(json_decode($contract->services) as $service)--}}
{{--                                                    <option value="{{ $service->code }}">{{ $service->code }} ({{ $service->name }})</option>--}}
{{--                                                @endforeach--}}
{{--                                            @endif--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            @endif--}}
                            <div class="col-lg-3 col-12">
                                <div class="form-group">
                                    <label class="d-block">&nbsp;</label>
                                    <button class="btn btn-success btn-block pull-right"><i class="fas fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Etiqueta MercadoLivre</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form method="POST" action="{{ route('supplier.settings.update_etiqueta_ml') }}">
                      
                        @csrf
                       
                        <div class="row">
                            <div class="col-lg-9 col-12">
                                <div class="form-group">
                                    <label>Impressão da Etiqueta</label>
                                    <div class="input-group">
                                    <select name="imp_etq_ml" id="imp_etq_ml" class="form-control" required>
                                       <option value="0" {{ old('imp_etq_ml') && old('imp_etq_ml') == 0 ? 'selected' : ($supplier && $supplier->imp_etq_ml == 0 ? 'selected' : '') }}>Pdf</option>
                                       <option value="1" {{ old('imp_etq_ml') && old('imp_etq_ml') == 1 ? 'selected' : ($supplier && $supplier->imp_etq_ml == 1 ? 'selected' : '') }}>Termica</option>
                                    </select> 
                                     </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="form-group">
                                    <label class="d-block">&nbsp;</label>
                                    <button class="btn btn-success btn-block pull-right"><i class="fas fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ trans('supplier.button_language_nav') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>Escolha o idioma da plataforma.</p>
                    <div class="form-group mb-0">
                        <select name="language-selector-name" id="language-selector" class="form-control">
                            <option value="">{{ trans('supplier.selecione_idioma_title') }}</option>
                            <option value="pt-br">Português</option>
                            <option value="zh">普通话</option>
                        </select>
                    </div>
                </div>
            </div>
                
            @if($authenticated_user->id == 56)
            {{-- Caso seja igual ao id da S2M2 --}}
                <div class="card shadow mt-4">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Configurações China Division</h3>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <form method="POST" action="{{ route('supplier.settings.update_china_division') }}">
                            @method('PUT')
                            @csrf                        
                            <p>Informe abaixo a apikey da sua conta China Division.</p>
                            <div class="row">
                                
                                    <div class="col-lg-9 col-12">
                                        <div class="form-group">
                                            <label>Api Key</label>
                                            <input type="text" name="china_division_apikey" placeholder="Insira aqui o seu apikey do China Division" class="form-control" value="{{ $authenticated_user->china_division_apikey ? $authenticated_user->china_division_apikey : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-12">
                                        <div class="form-group">
                                            <label class="d-block">&nbsp;</label>
                                            <button class="btn btn-success btn-block pull-right"><i class="fas fa-check"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                </div>

            @endif

            
            
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function getShippingPrice(){
            let product_id = $("select[name='product_id']").val();
            let from_zipcode = $("input[name='from_zipcode']").val();
            let to_zipcode = $("input[name='to_zipcode']").val();

            $.ajax({
                url: '/api/correios/simulate',
                method: 'GET',
                data: {from_zipcode: from_zipcode, to_zipcode : to_zipcode, product_id : product_id},
                beforeSend: function(){
                    $("#pac_price").html('<i class="fas fa-fw fa-spin fa-spinner"></i>');
                    /*$("#sedex_price").html('<i class="fas fa-fw fa-spin fa-spinner"></i>');*/
                },
                success: function(response){
                    if(response.status == 'success' && response.data){
                        let pac = parseFloat(response.data.pac);
                        /*let sedex = parseFloat(response.data.sedex);*/
                        let percentage = parseInt($("#percentage").val());

                        let discounted_pac = parseFloat(pac * (percentage / 100));
                        /*let discounted_sedex = parseFloat(sedex * (percentage / 100));*/

                        $("#pac_price").html(pac.toLocaleString('pt-BR',{style: 'currency', currency: 'BRL'}) + ' ('+percentage+'% = ' + discounted_pac.toLocaleString('pt-BR',{style: 'currency', currency: 'BRL'}) + ')');
                        /*$("#sedex_price").html(sedex.toLocaleString('pt-BR',{style: 'currency', currency: 'BRL'}) + ' ('+percentage+'% = ' + discounted_sedex.toLocaleString('pt-BR',{style: 'currency', currency: 'BRL'}) + ')');*/
                    }else{
                        Swal.fire("Erro", "Não foi possível calcular o frete para este produto. Verifique se as medidas e peso do produto estão cadastrados corretamente.", 'error');
                    }
                },
                error: function(response){
                    if(response.status == 'success' && response.data){
                        let pac = parseFloat(response.data.pac);
                        /*let sedex = parseFloat(response.data.sedex);*/
                        let percentage = parseInt($("#percentage").val());

                        let discounted_pac = parseFloat(pac * (percentage / 100));
                        /*let discounted_sedex = parseFloat(sedex * (percentage / 100));*/

                        $("#pac_price").html(pac.toLocaleString('pt-BR',{style: 'currency', currency: 'BRL'}) + ' ('+percentage+'% = ' + discounted_pac.toLocaleString('pt-BR',{style: 'currency', currency: 'BRL'}) + ')');
                        /*$("#sedex_price").html(sedex.toLocaleString('pt-BR',{style: 'currency', currency: 'BRL'}) + ' ('+percentage+'% = ' + discounted_sedex.toLocaleString('pt-BR',{style: 'currency', currency: 'BRL'}) + ')');*/
                    }else{
                        Swal.fire("Erro", "Não foi possível calcular o frete para este produto. Verifique se as medidas e peso do produto estão cadastrados corretamente.", 'error');
                    }
                }
            })
        }

        function updateShippingType(){
            let type = $("#shipping_method").val();

            $("#total_express_div").hide();
            $("#correios_div").hide();
            $("#no_shipping_div").hide();
            $("#melhor_envio_div").hide();

            if(type == 'correios'){
                $("#correios_div").show();
            }
            if(type == 'total_express'){
                $("#total_express_div").show();
            }

            if(type == 'no_shipping'){
                $("#no_shipping_div").show();
            }

            if(type == 'melhor_envio'){
                $("#melhor_envio_div").show();
            }
        }

        updateShippingType();
    </script>


<script>
    $(document).ready(function() {
        $('select[name="language-selector-name"]').on('change', function(event) {
            event.preventDefault();
            var lang = $(this).val();
            var date = new Date();
            date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
            document.cookie = "lang=" + lang + expires + "; path=/";
            location.reload();
        });
    });
</script>


@endsection
