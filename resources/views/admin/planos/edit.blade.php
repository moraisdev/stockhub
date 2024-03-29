@extends('admin.layout.default')

@section('title', 'Alterar Planos')

@section('content')
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
                            <h3 class="mb-0">Alterar Plano</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.planos.update', $planos->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">Detalhes do Plano</h6>
                        
                        <div class="row">   
                            <div class="col-6">
                            <div class="form-group">
                               <label class="form-control-label" for="name_title">Titulo</label>
                               <input type="text" id="titulo" class="form-control form-control-alternative" name="titulo" placeholder="Titulo do Plano" value="{{ old('titulo', $planos->titulo) }}">
                              </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                <label class="form-control-label" for="name_title">Descrição</label>
                                <input type="text" id="descricao" class="form-control form-control-alternative" name="descricao" placeholder="Descrição do Plano" value="{{ old('descricao', $planos->descricao) }}">
                                </div>
                            </div>

                    </div>

                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                            <label class="form-control-label" for="name_title">Valor</label>
                            <input type="text" id="valor" class="form-control form-control-alternative" name="valor" placeholder="Valor do Plano" value="{{ old('valor', $planos->valor) }}">
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group">
                             <label class="form-control-label" for="name_title">Ciclo Pagamento</label>
                             <select id="ciclo" class="form-control form-control-alternative" name="ciclo">
                                    <option {{old('ciclo',$planos->ciclo)=="Mensal"? 'selected':''}} value="Mensal">Mensal</option>
                                    <option {{old('ciclo',$planos->ciclo)=="Trimestral"? 'selected':''}} value="Trimestral">Trimestral</option>
                                    <option {{old('ciclo',$planos->ciclo)=="Semestral"? 'selected':''}} value="Semestral">Semestral</option>                                                 
                                    <option {{old('ciclo',$planos->ciclo)=="Anual"? 'selected':''}} value="Anual">Anual</option>                                                 
                          
                                </select>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group">
                             <label class="form-control-label" for="name_title">Status</label>
                               
                             <select id="status" class="form-control form-control-alternative" name="status">
                                    <option {{old('status',$planos->status)=="1"? 'selected':''}} value="1">Ativo</option>
                                    <option {{old('status',$planos->status)=="0"? 'selected':''}} value="0">Desativado</option>
                                   
                                </select>
                            </div>
                        </div>
   
                        <div class="col-3">
                            <div class="form-group">
                             <label class="form-control-label" for="name_title">Destaque</label>
                               
                             <select id="destaque" class="form-control form-control-alternative" name="destaque">
                                    <option {{old('destaque',$planos->destaque)=="0"? 'selected':''}} value="0">Não</option>
                                    <option {{old('destaque',$planos->destaque)=="1"? 'selected':''}} value="1">Sim</option>
                                   
                                </select>
                            </div>
                        </div> 


                    </div>    

                        <div class="form-group float-right">
                            <button class="btn btn-primary">Alterar Plano</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection