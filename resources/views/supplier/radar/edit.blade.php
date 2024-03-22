@extends('supplier.layout.default')

@section('stylesheets')
<style type="text/css">
    .thumbnail {
        width: 100%;
        height: 190px;
        background-size: cover;
        background-position: center;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 200px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <span class="mask bg-gradient-default opacity-8"></span>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Informações da Importação Coletiva') }}</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('supplier.radar.update', $radar->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select class="form-control form-control-alternative" id="status" name="status">
                                            <option value="EM ANALISE" {{ $radar->status == 'EM ANALISE' ? 'selected' : '' }}>Em Análise</option>
                                            <option value="PAGO" {{ $radar->status == 'PAGO' ? 'selected' : '' }}>Pago</option>
                                            <option value="REJEITADO" {{ $radar->status == 'REJEITADO' ? 'selected' : '' }}>Rejeitado</option>
                                            <option value="CANCELADO" {{ $radar->status == 'CANCELADO' ? 'selected' : '' }}>Cancelado</option>
                                            <option value="ATIVO" {{ $radar->status == 'ATIVO' ? 'selected' : '' }}>Ativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Cliente') }}</label>
                                        <input type="text" id="name" class="form-control form-control-alternative" name="name" placeholder="Nome do Cliente" value="{{ $radar->type_order == 1 ? $radar->shop->responsible_name : $radar->shop->corporate_name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="telefone">{{ __('Telefone') }}</label>
                                        <input type="text" id="phone" class="form-control form-control-alternative" name="phone" placeholder="Telefone" value="{{ $radar->shop->phone }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}</label>
                                        <input type="text" id="email" class="form-control form-control-alternative" name="email" placeholder="Email" value="{{ $radar->shop->email }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="documento">{{ __('Documento') }}</label>
                                        <input type="text" id="document" class="form-control form-control-alternative" name="document" placeholder="Documento" value="{{ $radar->type_order == 1 ? $radar->shop->responsible_document : $radar->shop->document }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="id">{{ __('ID do Cliente') }}</label>
                                        <input type="text" id="id" class="form-control form-control-alternative" name="id" placeholder="ID do Cliente" value="{{ $radar->shop->id }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="logradouro">{{ __('Logradouro') }}</label>
                                        <input type="text" id="street" class="form-control form-control-alternative" name="street" placeholder="Logradouro" value="{{ $radar->type_order == 1 ? $radar->shop->address->street : $radar->shop->address_business->street_company }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="numero">{{ __('Número') }}</label>
                                        <input type="text" id="numero" class="form-control form-control-alternative" name="numero" placeholder="Número" value="{{ $radar->type_order == 1 ? $radar->shop->address->number : $radar->shop->address_business->number_company }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="cep">{{ __('CEP') }}</label>
                                        <input type="text" id="zipcode" class="form-control form-control-alternative" name="zipcode" placeholder="CEP" value="{{ $radar->type_order == 1 ? $radar->shop->address->zipcode : $radar->shop->address_business->zipcode_company }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="pais">{{ __('País') }}</label>
                                        <input type="text" id="country" class="form-control form-control-alternative" name="country" placeholder="País" value="{{ $radar->type_order == 1 ? $radar->shop->address->country_company : $radar->shop->address_business->country_company }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="bairro">{{ __('Bairro') }}</label>
                                        <input type="text" id="district" class="form-control form-control-alternative" name="district" placeholder="Bairro" value="{{ $radar->type_order == 1 ? $radar->shop->address->district : $radar->shop->address_business->district_company }}" readonly>

                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="estado">{{ __('Estado') }}</label>
                                        <input type="text" id="state_code" class="form-control form-control-alternative" name="state_code" placeholder="Estado" value="{{ $radar->type_order == 1 ? $radar->shop->address->state_code : $radar->shop->address_business->state_code_company }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="cidade">{{ __('Cidade') }}</label>
                                        <input type="text" id="city" class="form-control form-control-alternative" name="city" placeholder="Cidade" value="{{ $radar->type_order == 1 ? $radar->shop->address->city : $radar->shop->address_business->city_company }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="complemento">{{ __('Complemento') }}</label>
                                        <input type="text" id="complement" class="form-control form-control-alternative" name="complement" placeholder="Complemento" value="{{ $radar->type_order == 1 ? $radar->shop->address->complement : $radar->shop->address_business->complement_company }}" readonly>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="complemento">{{ __('Data de Criação') }}</label>
                                        <input type="text" id="created_at" class="form-control form-control-alternative" name="created_at" placeholder="Data de Criação" value="{{ $radar->created_at }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="complemento">{{ __('Data da Ultima Atualização') }}</label>
                                        <input type="text" id="updated_at" class="form-control form-control-alternative" name="updated_at" placeholder="Data da Ultima Atualização" value="{{ $radar->updated_at }}" readonly>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">Radar</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-start">
                                    <a href="{{ route('supplier.download.documents', $radar->id) }}" class="btn btn-primary mr-2">Baixar Documentos</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer pb-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="float-right form-group">
                                    <a href="{{ route('supplier.radar.index') }}" class="btn btn-secondary">{{ __('supplier.cancel') }}</a>
                                    <button class="btn btn-primary">{{ __('supplier.save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
<script type="text/javascript">
    document.getElementById('downloadAllRadar').addEventListener('click', function() {
        var invoicePath = this.getAttribute('data-invoice-path');
        if (invoicePath) {
            window.open(invoicePath, '_blank');
        }
    });
</script>
@endsection
