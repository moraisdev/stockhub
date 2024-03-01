@extends('shop.layout.default')
<style>
.video-centered {
    display: block;
    margin: auto;
}


</style>
@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-default"></span>
        <div class="container-fluid">
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card shadow">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">Container Privado</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <p>
                            Assista o vídeo para entender os beneficios do container privado.<br>
                        </p>
                        <div class="card-body">
                        <img src=https://i.postimg.cc/k4Q1f53V/Design-sem-nome-2024-02-28-T180114-212.png" alt="Descrição da Imagem" width="560" height="315">
                        <div class="w-100 d-flex flex-wrap align-items-center justify-content-center mt-3">
                            <div class="button-container mx-2">
                                <a href="https://wa.me/5547997192065?text=Ol%C3%A1%2C+quero+importar+com+container+privado" class="btn btn-success">Importar Container Privado</a>
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