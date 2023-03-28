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
                        <h3 class="mb-0">Qual seu faturamento mensal?</h3>   
                        <div class="row">
                            <div class='col-md-6 p-5' style='padding-top: 80px !important; padding-bottom: 80px !important;'>
                                <h1 class='title-price-plan' id='slider-text'>Grátis</h1>
                                <div id="slider"></div>
                            </div>
                            <div class='col-md-6 text-center' id='full-text-plan'>
                                <h3>Como funciona nosso plano gratuito?</h3>
                                <small>Você paga apenas pelo que usúfruir</small>
                                <h1 class='title-price-plan'>Grátis</h1>
                                <small>+ Apenas R$2,30 a cada R$1.000 em pedidos pagos<br>
                                (cobrado por semana)</small>
                            </div>
                        </div>
                        <div class="row">
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
                        </div>
                        <div class="form-group text-center mt-4">
                            <a href='{{route('supplier.plans.selected', ['plan_id' => 6496])}}' id='test-plan' class="btn btn-primary w-100" style="color: #fff;"><i class="fas fa-check-circle"></i> TESTAR POR 14 DIAS GRÁTIS</a>
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
    function refreshPlan(){
        var valSlider = $( "#slider" ).slider( "value" );

        if(valSlider <= 14){
            $('#slider-text').html("Grátis")
            $('#full-text-plan').html(
                "<h3>Como funciona nosso plano gratuito?</h3>"+
                "<small>Você paga apenas pelo que usúfruir</small>"+
                "<h1 class='title-price-plan'>Grátis</h1>"+
                "<small>+ Apenas R$2,30 a cada R$1.000 em pedidos pagos<br>"+
                "(cobrado por semana)</small>"
            )
            $('#test-plan').attr('href', '{{route('supplier.plans.selected', ['plan_id' => 6496])}}')
        }

        if(valSlider > 14 && valSlider <= 28){
            $('#slider-text').html("Até R$25mil / mês")
            $('#full-text-plan').html(
                "<h3>Como funciona nossos planos pagos?</h3>"+
                "<small>Você paga apenas pelo faturamento líquido</small>"+
                "<h1 class='title-price-plan'>R$ 47,90</h1>"+
                "<small>Você pode fazer upgrade a qualquer"+
                "momento após ultrapassar o limite do plano<br>"+
                "(cobrado por mês)</small>"
            )
            $('#test-plan').attr('href', '{{route('supplier.plans.selected', ['plan_id' => 6496])}}')
        }
        if(valSlider > 28 && valSlider <= 42){
            $('#slider-text').html("Até R$50mil / mês")
            $('#full-text-plan').html(
                "<h3>Como funciona nossos planos pagos?</h3>"+
                "<small>Você paga apenas pelo faturamento líquido</small>"+
                "<h1 class='title-price-plan'>R$ 97,90</h1>"+
                "<small>Você pode fazer upgrade a qualquer"+
                "momento após ultrapassar o limite do plano<br>"+
                "(cobrado por mês)</small>"
            )
            $('#test-plan').attr('href', '{{route('supplier.plans.selected', ['plan_id' => 6496])}}')
        }
        if(valSlider > 42 && valSlider <= 56){
            $('#slider-text').html("Até R$100mil / mês")
            $('#full-text-plan').html(
                "<h3>Como funciona nossos planos pagos?</h3>"+
                "<small>Você paga apenas pelo faturamento líquido</small>"+
                "<h1 class='title-price-plan'>R$ 147,90</h1>"+
                "<small>Você pode fazer upgrade a qualquer"+
                "momento após ultrapassar o limite do plano<br>"+
                "(cobrado por mês)</small>"
            )
            $('#test-plan').attr('href', '{{route('supplier.plans.selected', ['plan_id' => 6496])}}')
        }
        if(valSlider > 56 && valSlider <= 70){
            $('#slider-text').html("Até R$300mil / mês")
            $('#full-text-plan').html(
                "<h3>Como funciona nossos planos pagos?</h3>"+
                "<small>Você paga apenas pelo faturamento líquido</small>"+
                "<h1 class='title-price-plan'>R$ 297,90</h1>"+
                "<small>Você pode fazer upgrade a qualquer"+
                "momento após ultrapassar o limite do plano<br>"+
                "(cobrado por mês)</small>"
            )
            $('#test-plan').attr('href', '{{route('supplier.plans.selected', ['plan_id' => 6496])}}')
        }
        if(valSlider > 70 && valSlider <= 84){
            $('#slider-text').html("Até R$500mil / mês")
            $('#full-text-plan').html(
                "<h3>Como funciona nossos planos pagos?</h3>"+
                "<small>Você paga apenas pelo faturamento líquido</small>"+
                "<h1 class='title-price-plan'>R$ 497,90</h1>"+
                "<small>Você pode fazer upgrade a qualquer"+
                "momento após ultrapassar o limite do plano<br>"+
                "(cobrado por mês)</small>"
            )
            $('#test-plan').attr('href', '{{route('supplier.plans.selected', ['plan_id' => 6496])}}')
        }
        if(valSlider > 84 && valSlider <= 100){
            $('#slider-text').html("+ de R$500mil / mês")
            $('#full-text-plan').html(
                "<h3>Entre em contato com suporte pelo email:"+
                "contato@mawapost.com"+
                "ou"+
                "pelo Whatsapp:"+
                "(11) 93423-5716</h3>"
            )
            $('#test-plan').attr('href', '{{route('supplier.plans.selected', ['plan_id' => 6496])}}')
        }
        
    }

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

