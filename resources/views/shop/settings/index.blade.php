@extends('shop.layout.default')

@section('title', 'Configuraçōes')

@section('content')
    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
            </div>
        </div>
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-md-6 mb-5 mb-xl-5">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <img src="{{asset('img/logo-woo.png')}}" style='height: 60px;' >
                                <h3 class="mb-0">Dados do Woocommerce</h3>
                            </div>
                        </div>
                    </div>
                    @php
                        $woocommerce_app = $authenticated_user->woocommerce_app;
                    @endphp
                    <div class="card-body">
                        <form method="POST" action="{{ route('shop.settings.update_woocommerce_app') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Domínio</label>
                                <div class="input-group">
                                    <input type="text" name="domain" class="form-control" placeholder="Dominio de sua loja woocommerce Ex: https://minhaloja.com" value="{{ ($woocommerce_app) ? $woocommerce_app->domain : '' }}" required >

                                </div>
                            </div>
                            <div class="form-group">
                                <label>APP Key</label>
                                <input type="text" name="app_key" class="form-control" placeholder="Chave do APP" value="{{ ($woocommerce_app) ? $woocommerce_app->app_key : '' }}" required >
                            </div>
                            <div class="form-group">
                                <label>APP Secret</label>
                                <input type="text" name="app_password" class="form-control" placeholder="Secret do APP" value="{{ ($woocommerce_app) ? $woocommerce_app->app_password : '' }}" required >
                            </div>
                            <div class="form-group d-none">
                                <input type="checkbox" class="mr-2" name="automatic_order_update" {{ ($woocommerce_app && $woocommerce_app->automatic_order_update) ? 'checked' : '' }}> Atualizar pedidos automaticamente
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary">Atualizar dados</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <div class="col-md-6 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <img src="{{asset('img/yampi-logo.png')}}" style='height: 30px;' >
                                <h3 class="mb-0">Dados da Yampi</h3>
                            </div>
                        </div>
                    </div>
                    @php
                        $yampi_app = $authenticated_user->yampi_app;
                    @endphp
                    <div class="card-body">
                        <form method="POST" action="{{ route('shop.settings.update_yampi_app') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Alias</label>
                                <div class="input-group">
                                    <input type="text" name="domain" class="form-control" placeholder="Alias" value="{{ ($yampi_app) ? $yampi_app->domain : '' }}" required >
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Token</label>
                                <div class="input-group">
                                    <input type="text" name="app_key" class="form-control" placeholder="Token" value="{{ ($yampi_app) ? $yampi_app->app_key : '' }}" required >
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Chave Secreta</label>
                                <input type="text" name="app_password" class="form-control" placeholder="Chave Secreta" value="{{ ($yampi_app) ? $yampi_app->app_password : '' }}" required >
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary">Atualizar dados</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>


            <div class="col-md-6 mb-5 mb-xl-5">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <img src="{{asset('img/logo-cartx.svg')}}" style='height: 30px;' >
                                <h3 class="mb-0">Dados do Cartx</h3>
                            </div>
                        </div>
                    </div>
                    @php
                        $cartx_app = $authenticated_user->cartx_app;
                    @endphp
                      <div class="card-body">
                        <form method="POST" action="{{ route('shop.settings.update_cartx_app') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                            <label>Domínio</label>
                                <div class="input-group">
                                    <input type="text" name="domain" class="form-control" placeholder="Dominio de sua loja cartx" value="{{ ($cartx_app) ? $cartx_app->domain : '' }}" required >
                                    <div class="input-group-append">
                                        <span class="input-group-text">.mycartpanda.com</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Token</label>
                                <input type="text" name="token" class="form-control" placeholder="Token da Loja" value="{{ ($cartx_app) ? $cartx_app->token : '' }}" required >
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary">Atualizar dados</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <div class="col-md-6 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <img src="{{asset('img/logo-bling.png')}}" style='height: 30px;' >
                                <h3 class="mb-0">Dados da Bling</h3>
                            </div>
                        </div>
                    </div>
                    @php

                    @endphp
                    <div class="card-body">
                        <form method="POST" action="{{ route('shop.profile.updatebling') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Chave da Api</label>
                                <input type="text" name="bling_apikey" class="form-control" placeholder="Insira aqui o código da API no Bling" value="{{ $authenticated_user->bling_apikey ? $authenticated_user->bling_apikey : '' }}" required >
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary">Atualizar dados</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
                 
            <div class="col-md-6 mb-5 mb-xl-5">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <img src="{{asset('img/logo-shopify.png')}}" style='height: 60px;' >
                                <h3 class="mb-0">Dados do Shopify</h3>
                            </div>
                        </div>
                    </div>
                    @php
                        $shopify_app = $authenticated_user->shopify_app;
                    @endphp
                    <div class="card-body">
                        <form method="POST" action="{{ route('shop.settings.update_shopify_app') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Domínio</label>
                                <div class="input-group">
                                    <input type="text" name="domain" class="form-control" placeholder="Dominio de sua loja shopify" value="{{ ($shopify_app) ? $shopify_app->domain : '' }}" required >
                                    <div class="input-group-append">
                                        <span class="input-group-text">.myshopify.com</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>APP Key</label>
                                <input type="text" name="app_key" class="form-control" placeholder="Chave do APP" value="{{ ($shopify_app) ? $shopify_app->app_key : '' }}" required >
                            </div>
                            <div class="form-group">
                                <label>APP Secret</label>
                                <input type="text" name="app_password" class="form-control" placeholder="Senha do APP" value="{{ ($shopify_app) ? $shopify_app->app_password : '' }}" required >
                            </div>
                            <div class="form-group d-none">
                                <input type="checkbox" class="mr-2" name="automatic_order_update" {{ ($shopify_app && $shopify_app->automatic_order_update) ? 'checked' : '' }}> Atualizar pedidos automaticamente
                            </div>
                            <div class="form-group">
                                <label>Token</label>
                                <input type="text" name="token" class="form-control" placeholder="Token único" value="{{ ($shopify_app) ? $shopify_app->token : '' }}">
                            </div>
                            <div class="form-group">
                                <label>API Versão</label>
                                <input type="text" name="api_version" class="form-control" placeholder="API Versão 2023-01" value="{{ ($shopify_app) ? $shopify_app->api_version : '' }}">
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary">Atualizar dados</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>


            <div class="col-md-6 mb-5 mb-xl-5">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <img src="{{asset('img/ml2.png')}}" style='height: 60px;' >
                                <h3 class="mb-0">Dados do Mercadolivre</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('shop.settings.update_mercadolivre_app') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label>ID do Aplicativo</label>
                                <input type="text" name="app_id" class="form-control" placeholder="Chave do APP" value="{{ ($mercaodlivreapi) ? $mercaodlivreapi->app_id : '' }}" required >
                            </div>
                            <div class="form-group">
                                <label>Chave secreta</label>
                                <input type="text" name="secret_id" class="form-control" placeholder="Cliente Secret" value="{{ ($mercaodlivreapi) ? $mercaodlivreapi->secret_id : '' }}" required >
                            </div>
                                                        
                            <div class="form-group">
                                <label>Tipo de Anuncio</label>
                            <select id="tipo_anuncio" class="form-control form-control-alternative" name="tipo_anuncio">
                                    @if(isset($mercaodlivreapi->tipo_anuncio))
                                    <option {{old('tipo_anuncio',$mercaodlivreapi->tipo_anuncio)=="gold_special"? 'selected':''}} value="gold_special">Clássica</option>
                                    <option {{old('tipo_anuncio',$mercaodlivreapi->tipo_anuncio)=="gold_pro"? 'selected':''}} value="gold_pro">Premium</option>
                                    @else
                                    <option value="gold_special">Clássica</option>
                                    <option value="gold_pro">Premium</option>
                                    @endif

                                </select>
                                </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary">Atualizar dados</button>
                            </div>
                        </form>

                        <p>Instruções.</p>
                        <span> 1º - Crie uma aplicação no Mercado Livre, Link abaixo:</span>
                        <p><a href="https://developers.mercadolivre.com.br/devcenter" target="_blank">https://developers.mercadolivre.com.br/devcenter</a>.</p>
                        <p>2º - URI de redirect *:</p>
                        <span>{{ env('SITE_URL') }}</span>
                        <p>3º -  Configuração de notificações </p>
                        <span>{{ env('APP_URL') }}/api/mercadolivre_callback</span>
                        <p>Obs. De todas as permisões ao App criado</p>
                        <span>Copie e cole suas credenciais</span>
                      
                        

                    </div>

                </div>
            </div>
           

            <div class="col-md-6 mb-5 mb-xl-5">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <img src="{{asset('img/shopee.png')}}" style='height: 60px;' >
                                <h3 class="mb-0">Dados do Shoppe</h3>
                                <h3 class="mb-0">Em Breve</h3>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                   
                        <form method="POST" action="{{ route('shop.settings.update_shoppe_app') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label>APP Link</label>
                                <input type="text" name="app_id" class="form-control" placeholder="Link App" value="" required >
                            </div>
                            <div class="form-group">
                                <label>Shop Id</label>
                                <input type="text" name="secret_id" class="form-control" placeholder="id shop" value="" required >
                            </div>
                                                        
                            <div class="form-group">
                                <label>Partner Id</label>
                                <input type="text" name="tipo_anuncio" class="form-control" placeholder="Codigo" value="">
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary">Atualizar dados</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
           



        </div>
    </div>
@endsection
