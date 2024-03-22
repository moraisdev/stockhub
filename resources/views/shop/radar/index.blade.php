@extends('shop.layout.default')
<style>
    .video-centered {
        display: block;
        margin: auto;
        max-width: 70%;
        height: auto;
    }
    .status-dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 0px;
    }
    .status-text {
        font-size: 14px;
    }
    .status-EM_ANALISE {
        background-color: #FFEB3B; /* Amarelo */
        color: #212529; /* Texto escuro para melhor contraste */
    }
    .status-ATIVO {
        background-color: #4CAF50; /* Verde */
        color: #FFFFFF;
    }
    .status-DESATIVADO {
        background-color: #F44336; /* Vermelho */
        color: #FFFFFF;
    }
    .video-text {
        text-align: center;
    }
</style>

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-default"></span>
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">STATUS</h5>
                                    @php
                                        $statusClass = 'status-' . str_replace(' ', '_', strtoupper($shopRadar->status ?? 'DESATIVADO'));
                                    @endphp
                                    <span class="status-dot {{ $statusClass }}"></span>
                                    <span class="status-text {{ $statusClass }}">{{ $shopRadar->status ?? 'Desativado' }}</span>
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
        <div class="col-12 mb-3">
            <div class="card shadow">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">Radar Siscomex</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="video-text"> <!-- Adicionado para centralizar o texto -->
                        <p>
                            Assista o vídeo abaixo para entender como ativar seu radar<br>
                        </p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center justify-content-center"> <!-- Alterado para centralizar a imagem e os botões -->
                    <img src="https://i.postimg.cc/C5y0f5Cz/Design-sem-nome-2024-02-28-T175742-161.png" alt="Descrição da Imagem" class="video-centered">
                    <div class="w-100 d-flex flex-wrap align-items-center justify-content-center mt-3">
                    @if(isset($shopRadar) && ($shopRadar->status !== 'EM ANALISE' && $shopRadar->status !== 'PAGO'))
                        <div class="button-container mx-2">
                            <a href="/shop/radar/buy" class="btn btn-success">Comprar Radar</a>
                        </div>
                        <div class="button-container mx-2">
                            <a href="/shop/radar/activate" class="btn btn-primary">Ativar Radar</a>
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            @if($shopRadar->status === 'EM ANALISE')
                                Seu pedido de Radar está atualmente em análise. Em breve entraremos em contato.
                            @elseif($shopRadar->status === 'PAGO')
                                Seu pedido de Radar foi pago com sucesso. Em breve entraremos em contato para os próximos passos.
                            @endif
                        </div>
                    @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
