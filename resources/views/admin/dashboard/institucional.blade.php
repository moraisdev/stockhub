@extends('admin.layout.default')

@section('title', 'Alterar Site')

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1 class="display-2 text-white">Alterar Site</h1>
                <a href="{{ route('admin.tutorial.index') }}" class="btn btn-secondary">Voltar</a>
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
                            <h3 class="mb-0">Alterar Site Institucional</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.institucional.update', $institucional->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">Titulo Principal</label>
                            <input type="text" id="tituloprincipal" class="form-control form-control-alternative" name="tituloprincipal" placeholder="titulo principal" value="{{ old('tituloprincipal', $institucional->tituloprincipal) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-control-label" for="name_title">Sub Titulo Principal</label>
                            <input type="text" id="tituloprincipal2" class="form-control form-control-alternative" name="tituloprincipal2" placeholder="sub titulo" value="{{ old('tituloprincipal2', $institucional->tituloprincipal2) }}">
                        </div>
                        

                    <div class="row">   
                        <div class="col-4">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Botão Aderir</label>
                            <input type="text" id="titulobutaoaderir" class="form-control form-control-alternative" name="titulobutaoaderir" placeholder="sub titulo" value="{{ old('titulobutaoaderir', $institucional->titulobutaoaderir) }}">
                          </div>
                        </div> 

                        <div class="col-8">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Titulo Beneficios</label>
                            <input type="text" id="titulobeneficios" class="form-control form-control-alternative" name="titulobeneficios" placeholder="sub titulo" value="{{ old('titulobeneficios', $institucional->titulobeneficios) }}">
                           </div>
                        </div> 

                    </div>

                    <div class="row">   
                        <div class="col-4">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Titulo Serviços</label>
                            <input type="text" id="tituloservicos" class="form-control form-control-alternative" name="tituloservicos" placeholder="sub titulo" value="{{ old('tituloservicos', $institucional->tituloservicos) }}">
                          </div>
                        </div> 

                        <div class="col-8">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Descrição Serviços</label>
                            <input type="text" id="servicos" class="form-control form-control-alternative" name="servicos" placeholder="sub titulo" value="{{ old('servicos', $institucional->servicos) }}">
                           </div>
                        </div> 

                    </div>


                    <div class="row">   
                        <div class="col-4">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Duvida Item 1</label>
                            <input type="text" id="faq1" class="form-control form-control-alternative" name="faq1" placeholder="sub titulo" value="{{ old('faq1', $institucional->faq1) }}">
                          </div>
                        </div> 

                        <div class="col-8">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Descrição</label>
                            <input type="text" id="descricaofaq1" class="form-control form-control-alternative" name="descricaofaq1" placeholder="sub titulo" value="{{ old('descricaofaq1', $institucional->descricaofaq1) }}">
                           </div>
                        </div> 

                    </div>

                    <div class="row">   
                        <div class="col-4">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Duvida Item 2</label>
                            <input type="text" id="faq2" class="form-control form-control-alternative" name="faq2" placeholder="sub titulo" value="{{ old('faq2', $institucional->faq2) }}">
                          </div>
                        </div> 

                        <div class="col-8">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Descrição</label>
                            <input type="text" id="descricaofaq2" class="form-control form-control-alternative" name="descricaofaq2" placeholder="sub titulo" value="{{ old('descricaofaq2', $institucional->descricaofaq2) }}">
                           </div>
                        </div> 

                    </div>

                    <div class="row">   
                        <div class="col-4">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Duvida Item 3</label>
                            <input type="text" id="faq3" class="form-control form-control-alternative" name="faq3" placeholder="sub titulo" value="{{ old('faq3', $institucional->faq3) }}">
                          </div>
                        </div> 

                        <div class="col-8">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Descrição</label>
                            <input type="text" id="descricaofaq3" class="form-control form-control-alternative" name="descricaofaq3" placeholder="sub titulo" value="{{ old('descricaofaq3', $institucional->descricaofaq3) }}">
                           </div>
                        </div> 

                    </div>

                    <div class="row">   
                        <div class="col-4">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Duvida Item 4</label>
                            <input type="text" id="faq4" class="form-control form-control-alternative" name="faq4" placeholder="sub titulo" value="{{ old('faq4', $institucional->faq4) }}">
                          </div>
                        </div> 

                        <div class="col-8">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Descrição</label>
                            <input type="text" id="descricaofaq4" class="form-control form-control-alternative" name="descricaofaq4" placeholder="sub titulo" value="{{ old('descricaofaq4', $institucional->descricaofaq4) }}">
                           </div>
                        </div> 

                    </div>

                    <div class="row">   
                        <div class="col-4">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Duvida Item 5</label>
                            <input type="text" id="faq5" class="form-control form-control-alternative" name="faq5" placeholder="sub titulo" value="{{ old('faq5', $institucional->faq5) }}">
                          </div>
                        </div> 

                        <div class="col-8">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Descrição</label>
                            <input type="text" id="descricaofaq5" class="form-control form-control-alternative" name="descricaofaq5" placeholder="sub titulo" value="{{ old('descricaofaq5', $institucional->descricaofaq5) }}">
                           </div>
                        </div> 

                    </div>


                    <div class="row">   
                        <div class="col-12">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Endereço</label>
                            <input type="text" id="endereco" class="form-control form-control-alternative" name="endereco" placeholder="sub titulo" value="{{ old('endereco', $institucional->endereco) }}">
                          </div>
                        </div> 
                    </div>

                    <div class="row">   
                        <div class="col-6">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Telefone</label>
                            <input type="text" id="telefone1" class="form-control form-control-alternative" name="telefone1" placeholder="sub titulo" value="{{ old('telefone1', $institucional->telefone1) }}">
                          </div>
                        </div> 

                        <div class="col-6">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Telefone</label>
                            <input type="text" id="telefone2" class="form-control form-control-alternative" name="telefone2" placeholder="sub titulo" value="{{ old('telefone2', $institucional->telefone2) }}">
                           </div>
                        </div> 

                    </div>


                    <div class="row">   
                        <div class="col-4">
                          <div class="form-group">
                            <label class="form-control-label" for="name_title">Email</label>
                            <input type="text" id="email1" class="form-control form-control-alternative" name="email1" placeholder="sub titulo" value="{{ old('email1', $institucional->email1) }}">
                          </div>
                        </div> 

                        <div class="col-8">
                           <div class="form-group">
                            <label class="form-control-label" for="name_title">Email</label>
                            <input type="text" id="email2" class="form-control form-control-alternative" name="email2" placeholder="sub titulo" value="{{ old('email2', $institucional->email2) }}">
                           </div>
                        </div> 

                    </div>



                    <div class="form-group float-right">
                            <button class="btn btn-primary">Alterar</button>
                        </div>


                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection