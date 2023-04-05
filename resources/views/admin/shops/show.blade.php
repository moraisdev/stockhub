@extends('admin.layout.default')

@section('title', __('supplier.detalhes_lojista'))

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1 class="display-2 text-white">{{ $shop->name }}</h1>
                <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">{{ trans('supplier.back') }}</a>
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
                            <h3 class="mb-0">{{ trans('supplier.detalhes_lojista') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.name_store') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ $shop->name }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Seu nome</label>
                                <input type="text" class="form-control" name="responsible_name" value="{{ $shop->responsible_name }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.text_email') }}</label>
                                <input type="email" class="form-control" value="{{ $shop->email }}" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.text_phone') }}</label>
                                <input type="text" class="form-control phone" name="phone" value="{{ $shop->phone }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Tipo de Documento</label>
                                <select class="form-control" id="document_type" readonly>
                                    <option value="1" {{ strlen($shop->document) == 11 ? 'selected' : '' }}>CPF</option>
                                    <option value="2" {{ strlen($shop->document) != 11 ? 'selected' : '' }}>{{ trans('supplier.cnpj') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label" id="document_label">{{ trans('supplier.cpf_cnpj') }}</label>
                                <input type="text" class="form-control" name="document" id="document" value="{{ $shop->document }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-secondary shadow mt-4">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ trans('supplier.adress') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.logradouro') }}</label>
                                <input type="text" class="form-control" name="street" value="{{ $shop->address ? $shop->address->street : '' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.numero') }}</label>
                                <input type="text" class="form-control" name="number" value="{{ $shop->address ? $shop->address->number : '' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.brotherhood') }}</label>
                                <input type="text" class="form-control" name="district" value="{{ $shop->address ? $shop->address->district : '' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.complemment') }}</label>
                                <input type="text" class="form-control" name="complement" value="{{ $shop->address ? $shop->address->complement : '' }}" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.postal_code') }}</label>
                                <input type="text" class="form-control cep" name="zipcode" value="{{ $shop->address ? $shop->address->zipcode : '' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.pais') }}</label>
                                <select class="form-control" name="contry" id="country_select" readonly>
                                    <option value="Brasil" {{ !$shop->address || ($shop->address && $shop->address->country == 'Brasil') ? 'selected' : '' }}>Brasil</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.estado') }}</label>
                                <select class="form-control" name="state_code" id="state_select" readonly>
                                    <option value="">{{ $shop->address ? $shop->address->state_code : 'Não selecionado' }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ trans('supplier.city') }}</label>
                                <select class="form-control" name="city" id="city_select" readonly>
                                    <option value="">{{ $shop->address ? $shop->address->city : 'Não selecionada' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
