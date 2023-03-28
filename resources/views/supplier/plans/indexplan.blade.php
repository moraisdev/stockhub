@extends('supplier.layout.default')

@section('title', config('app.name').' - Planos Fornecedor')

@section('content')
    <style>
        .ui-widget-header{
            background: linear-gradient(90deg,#005b91,#0085de) !important;
        }
        .ui-state-default{
            box-shadow: 1px 1px 6px #424242 !important;
            height: 30px !important;
            width: 30px !important;
            border-radius: 50px !important;
            background: #0064a8 !important;
            cursor: pointer !important;
            -webkit-appearance: none !important;
            top: -.3em !important;
            border: none !important;
        }

        .ui-state-default:focus { outline: none !important; }

        .ui-widget.ui-widget-content{
            border: none !important;
            background-color: #eee !important;
            height: 20px;
        }
        
        .title-price-plan{
            font-size: 1.5rem;
            font-weight: 700;
            color: #0064a8;
        }

        .check-green-plan{
            color: #0064a8;
        }

        .bg-plan-anual{
            /* background: linear-gradient(40deg, #2464a4 0, #2464a4 40%, #1e5b8e 100%) !important; */
            background: linear-gradient(40deg, #ff9000 0, #ff9000 40%, #ffb400 100%) !important;
        }

        .bg-plan-anual h1, .bg-plan-anual h3, .bg-plan-anual p, .bg-plan-anual small, .bg-plan-anual h4, .bg-plan-anual h2{
            color: #fff !important;
        }

        .bg-plan-anual h1, .bg-plan h1{
            font-size: 28pt;
            font-weight: bold;
        }

        .bg-plan-anual button{
            /* background-color: #FBB345;
            border: none; */
        }

        .bg-plan{
            background-color: #f2f5f8;
            padding-top: 120px;
            padding-bottom: 120px;
        }
    </style>
    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
            </div>
        </div>
    </div>
    <div class="container-fluid mt--7">
        <div class="row justify-content-center">
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    {{-- <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                                             
                            </div>
                        </div>
                    </div> --}}

                  
                    <div class="card-body">
                        <div class="row">
                        @forelse($planos as $plano) 
                        @if ($plano->destaque == 0)
                        <div class="col-md-4 mb-2 mb-xl-0 btn mr-0 bg-plan">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <br>
                                            <br>
                                            <br>
                                            
                                            <h2 class="mb-3"><br>{{$plano->titulo}}</h2>
                                            <h1 class="mb-1">R$ {{$plano->valor}}</h1>                                 
                                        </div> 
                                    </div><br>
                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                            
                                            <h4><i>Ciclo {{$plano->ciclo}}</i></h4>
                                        </div>
                                        <div class="form-group text-center">
                                        <a href="{{route('supplier.plans.selected', $plano->id)}}"  class="btn btn-primary">Assine Agora</a>
                                        </div>
                                    </div>                    
                                </div>
                            </div>
                            @endif 
                        
                            @if ($plano->destaque == 1)
                        <div class="col-md-4 mb-2 mb-xl-0 btn mr-0 bg-plan-anual pt-4 pb-4">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                        <img class='mb-1' src="{{asset('assets/img/icon plan anual.png')}}" alt="">
                                            <h2 class="mb-3"><br>{{$plano->titulo}}</h2>
                                            <h1 class="mb-1">R$ {{$plano->valor}}</h1>                                 
                                        </div> 
                                    </div><br>
                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <h4><i>Ciclo {{$plano->ciclo}}</i></h4>
                                        </div>
                                        <div class="form-group text-center">
                                               
                                        <a href="{{route('supplier.plans.selected', $plano->id)}}" class="btn btn-secondary" >Assine Agora</a>
                                        </div>
                                    </div>                    
                                </div>
                            </div>
                            @endif 
                        
                            
                            @endforeach                          
                            
                                                        
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6 mb-5 mb-xl-0">
                                <div class="form-group">
                                    <h2 class="mb-4 text-center">Tudo que você Fornecedor precisa está aqui:</h4>
                                    <p><i class="fa fa-check"></i> Sincronização com Gateways de pagamento</p>
                                    <p><i class="fa fa-check"></i> Análise top produtos mais rentáveis;</p>
                                    <p><i class="fa fa-check"></i> Integrações;</p>
                                   
                                </div>
                            </div>
                            <div class="col-md-6 mb-5 mb-xl-0">
                            <div class="form-group">
                                    <h2 class="mb-4 text-center">Características das assinaturas:</h4>
                                    <p><i class="fa fa-check"></i> Opção de Logística integrada com a plataforma;</p>
                                    <p><i class="fa fa-check"></i> Vendas ilimitadas;</p>
                                    <p><i class="fa fa-check"></i> Suporte online.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        {{-- <div class="row mb-4">
            @foreach ($planos as $plano)
                <div class="col-md-3 mb-4">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">{{$plano->Name}}</h3>                                
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <p>
                                    {{$plano->Description}}<br>
                                    <b>R$ {{str_replace('.', ',', $plano->Amount)}} / {{$plano->PlanFrequence->Name}}</b>
                                    <br>
                                </p>
                            </div>
                            <div class="form-group text-center">
                                <a href='{{route('supplier.plans.selected', ['plan_id' => $plano->Id])}}' class="btn btn-primary" style="color: #fff;"><i class="fas fa-check-circle"></i> Selecionar Plano</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div> --}}
    </div>
@endsection

@section('scripts')
<script>

    $(document).ready(function(){
        
        $( "#slider" ).slider({
            animate: "slow",
            orientation: "horizontal",
            range: "min",
            max: 100,
            value: 0,
            slide: refreshPlan,
            change: refreshPlan
        });
    })
</script>
@endsection

