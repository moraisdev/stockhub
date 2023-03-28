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
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    {{-- <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                                             
                            </div>
                        </div>
                    </div> --}}
                    <div class="card-body">
                        <div class="row justify-content-center mb-4">
                            <div class='col-md-6 text-center' id='full-text-plan'>
                                <h3>Como funciona nosso plano gratuito?</h3>
                                <small>Você paga apenas pelo que usúfruir</small>
                                <h1 class='title-price-plan'>Grátis</h1>
                                <small>Acima de R$ 999,90 em pedidos processados é cobrado uma taxa proporcional de acordo com o valor processado.<br>
                                (cobrado por semana)</small>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <h3 class='text-center'>Benefícios do Plano</h3>
                            </div>
                            <div class="col">
                                <p><i class="fas fa-check check-green-plan"></i> Lojas ilimitadas</p>
                                <p><i class="fas fa-check check-green-plan"></i> Cálculo do Lucro Líquido</p>
                                <p><i class="fas fa-check check-green-plan"></i> Sincronização com Gateways de pagamento e ferramentas de Marketing</p>
                                <p><i class="fas fa-check check-green-plan"></i> Análise top produtos mais rentáveis</p>
                            </div>
                            <div class="col">
                                <p><i class="fas fa-check check-green-plan"></i> Integração com AliExpress</p>
                                <p><i class="fas fa-check check-green-plan"></i> Integração com Dsers</p>
                                <p><i class="fas fa-check check-green-plan"></i> Audience Insights</p>
                                <p><i class="fas fa-check check-green-plan"></i> Precificação 2.0</p>
                            </div>
                        </div> --}}
                        <form action="{{route('supplier.plans.store.cancel')}}" method='post'>
                            @csrf
                            <h3 class='text-center mb-4'>Tem certeza que deseja cancelar o plano atual e voltar para o plano gratuito?</h3>
                            <div class="w-100 d-flex flex-wrap align-items-center justify-content-center">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <select name="option_cancel" id="option_cancel" class='form-control'>
                                            <option value="opcao_1">Opção 1</option>
                                            <option value="opcao_2">Opção 2</option>
                                            <option value="opcao_3">Opção 3</option>
                                            <option value="opcao_4">Opção 4</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <textarea class='form-control d-none' name="reason_cancellation" id="reason_cancellation" rows="3" placeholder='*Motivo do cancelamento'></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-group text-center">
                                    <button type='submit' class="btn btn-success" style="color: #fff;"><i class="fas fa-supplierping-cart"></i> Cancelar Plano</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('#option_cancel').on('change', function(){
                if($(this).val() == 'outro'){
                    $('#reason_cancellation').removeClass('d-none')
                }else{
                    $('#reason_cancellation').addClass('d-none')
                }
            })
        })
    </script>
@endsection

