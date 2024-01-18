@extends('admin.layout.default')

@section('title', 'Alterar categoria')

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1 class="display-2 text-white">Alterar categoria</h1>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
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
                            <h3 class="mb-0">Alterar categoria</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">Detalhes da categoria</h6>
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">Nome da categoria</label>
                            <input type="text" id="name_title" class="form-control form-control-alternative" name="name" placeholder="Nome da categoria" value="{{ old('name', $category->name) }}">
                        </div>
                        <div class="form-group float-right">
                            <button class="btn btn-primary">Alterar categoria</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection