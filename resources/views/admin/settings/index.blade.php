@extends('admin.layout.default')

@section('title', __('supplier.configuracoes_title'))

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
        </div>
    </div>
</div>
<div class="container-fluid mt--7">

<div class="row">
        <div class="col-xl-12 order-xl-2">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h3 class="mb-0">{{ trans('supplier.configuracoes_gerais') }}</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.settings.update_settings', $admin->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                    <div class="row">   
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">{{ trans('supplier.plano_fornecedor') }}</label>
                            <select id="plano_f" class="form-control form-control-alternative" name="plano_f">
                                    <option {{old('plano_f',$admin->plano_f)=="0"? 'selected':''}} value="0">{{ trans('supplier.gratuito') }}</option>
                                    <option {{old('plano_f',$admin->plano_f)=="1"? 'selected':''}} value="1">{{ trans('supplier.gerencianet') }}</option>
                                    <option {{old('plano_f',$admin->plano_f)=="2"? 'selected':''}} value="2">{{ trans('supplier.safrapay') }}</option>                                                 
                            </select> 
                        </div>
                    </div> 
                    
                    
                    <div class="col-4"> 
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">{{ trans('supplier.plano_lojista') }}</label>
                            <select id="plano_shop" class="form-control form-control-alternative" name="plano_shop">
                                    <option {{old('plano_shop',$admin->plano_shop)=="0"? 'selected':''}} value="0">{{ trans('supplier.gratuito') }}</option>
                                    <option {{old('plano_shop',$admin->plano_shop)=="1"? 'selected':''}} value="1">{{ trans('supplier.gerencianet') }}</option>
                                    <option {{old('plano_shop',$admin->plano_shop)=="2"? 'selected':''}} value="2">{{ trans('supplier.safrapay') }}</option>                                                 
                            </select> 
                        </div>
                    </div>

                    <div class="col-4"> 
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">{{ trans('supplier.dias_gratuidos') }}</label>
                            <select id="free_shop" class="form-control form-control-alternative" name="free_shop">
                                    <option {{old('free_shop',$admin->free_shop)=="7"? 'selected':''}} value="7">{{ trans('supplier.sete_dias') }}</option>
                                    <option {{old('free_shop',$admin->free_shop)=="14"? 'selected':''}} value="14">{{ trans('supplier.quator_dias') }}</option>
                                    <option {{old('free_shop',$admin->free_shop)=="30"? 'selected':''}} value="30">{{ trans('supplier.trinta_dias') }}</option>                                                 
                            </select> 
                        </div>
                    </div>

                </div>

                <div class="row">   
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">{{ trans('supplier.cadastro_fornecedor') }}</label>
                            <select id="cad_supplier" class="form-control form-control-alternative" name="cad_supplier">
                                    <option {{old('cad_supplier',$admin->cad_supplier)=="0"? 'selected':''}} value="0">{{ trans('supplier.sim') }}</option>
                                    <option {{old('cad_supplier',$admin->cad_supplier)=="1"? 'selected':''}} value="1">{{ trans('supplier.nao') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">{{ trans('supplier.cadastro_shop') }}</label>
                            <select id="cad_shop" class="form-control form-control-alternative" name="cad_shop">
                                    <option {{old('cad_shop',$admin->cad_shop)=="0"? 'selected':''}} value="0">{{ trans('supplier.sim') }}</option>
                                    <option {{old('cad_shop',$admin->cad_shop)=="1"? 'selected':''}} value="1">{{ trans('supplier.nao') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">{{ trans('supplier.catalago') }}</label>
                            <select id="catalogo" class="form-control form-control-alternative" name="catalogo">
                                    <option {{old('catalogo',$admin->catalogo)=="0"? 'selected':''}} value="0">{{ trans('supplier.sim') }}</option>
                                    <option {{old('catalogo',$admin->catalogo)=="1"? 'selected':''}} value="1">{{ trans('supplier.nao') }}</option>
                            </select>    
                        
                        
                        
                        </div>
                    </div>
                </div>    
                
                

                <div class="row">   
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">Bloquear Acesso</label>
                            <select id="bloq_acesso" class="form-control form-control-alternative" name="bloq_acesso">
                                    <option {{old('bloq_acesso',$admin->bloq_acesso)=="3"? 'selected':''}} value="3">3 Dias</option>
                                    <option {{old('bloq_acesso',$admin->bloq_acesso)=="7"? 'selected':''}} value="7">{{ trans('supplier.sete_dias') }}</option>
                                    <option {{old('bloq_acesso',$admin->bloq_acesso)=="15"? 'selected':''}} value="15">15 Dias</option>                                                 
                            </select>
                        </div>
                    </div>


                  
                </div>


                <div class="col-12">
                            <h3 class="mb-0">Forma de Pagamento Aceita</h3>
                </div>

                <div class="row">   
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">Pix</label>
                            <select id="pg_pix" class="form-control form-control-alternative" name="pg_pix">
                                    <option {{old('pg_pix',$admin->pg_pix)=="0"? 'selected':''}} value="0">{{ trans('supplier.sim') }}</option>
                                    <option {{old('pg_pix',$admin->pg_pix)=="1"? 'selected':''}} value="1">{{ trans('supplier.nao') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">Boleto Bancario</label>
                            <select id="pg_boleto" class="form-control form-control-alternative" name="pg_boleto">
                                    <option {{old('pg_boleto',$admin->pg_boleto)=="0"? 'selected':''}} value="0">{{ trans('supplier.sim') }}</option>
                                    <option {{old('pg_boleto',$admin->pg_boleto)=="1"? 'selected':''}} value="1">{{ trans('supplier.nao') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">Cartão de Credito</label>
                            <select id="pg_cartao" class="form-control form-control-alternative" name="pg_cartao">
                                    <option {{old('pg_cartao',$admin->pg_cartao)=="0"? 'selected':''}} value="0">{{ trans('supplier.sim') }}</option>
                                    <option {{old('pg_cartao',$admin->pg_cartao)=="1"? 'selected':''}} value="1">{{ trans('supplier.nao') }}</option>
                            </select>    
                        
                        
                        
                        </div>
                    </div>
                
                </div> 
                        

                        <div class="form-group float-right">
                            <button class="btn btn-primary">Alterar Configurações</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>
    
@if ($admin->conf_melhor_envios == 1) 
    <div class="row">      
    <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Configurações Melhor Envio</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-4">
                        <p>Usar Melhor Envio como base de cálculo para os seus fretes. Lembrando que é somente uma estimativa, a etiqueta tem que ser impressa manualmente pelo fornecedor.</p>
                        <form method="POST" action="{{ route('admin.settings.update_settings') }}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <h2>{{ trans('supplier.conta') }} 1</h2>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>{{ trans('supplier.name') }}</label>
                                        <input type="text" name="name" class="form-control" placeholder="Nome da conta" value="{{ $melhorEnvioSettings1 ? $melhorEnvioSettings1->name : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Client ID</label>
                                        <input type="text" name="client_id" class="form-control" placeholder="Client ID" value="{{ $melhorEnvioSettings1 ? $melhorEnvioSettings1->client_id : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Secret</label>
                                        <input type="text" name="secret" class="form-control" placeholder="Secret" value="{{ $melhorEnvioSettings1 ? $melhorEnvioSettings1->secret : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-center">
                                        <label class="d-block">&nbsp;</label>
                                        <button class="btn btn-success pull-right mb-3"><i class="fas fa-check"></i> Autorizar</button>
                                        {{-- <small><a href="{{route('supplier.settings.melhor_envio.remove')}}">Remover autorização</a></small> --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>



            </div>
        </div>
        @endif
    </div>
    


@endsection