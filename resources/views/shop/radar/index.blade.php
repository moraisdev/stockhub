@extends('shop.layout.default')
<style>
.video-centered {
    display: block;
    margin: auto;
}
.status-dot {
    height: 8px;
    width: 8px;
    background-color: #ee3a1f;
    border-radius: 50%;
    display: inline-block;
    margin-left:0px;
}

.status-text {
    color: #ee3a1f;
    font-size: 14px;
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
                                    <span class="status-dot"></span>

                                    <span class="status-text">Desativado</span>
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
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <p>
                            Para utilizar nosso sistema você precisa ter um radar ativo.<br>
                        </p>
                        <div class="card-body">
                            <iframe class="video-centered" width="560" height="315" src="https://www.youtube.com/embed/rSri-GH7ZcQ?si=u3wlAlb99cAjOGIv" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>                        </div>
                        <div class="w-100 d-flex flex-wrap align-items-center justify-content-center mt-3">
                            <div class="button-container mx-2">
                                <a href="/shop/radar/buy" class="btn btn-success">Comprar Radar</a>
                            </div>
                            <div class="button-container mx-2">
                                <a href="/shop/radar/activate" class="btn btn-primary">Ativar Radar</a>
                            </div>        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')


@endsection
