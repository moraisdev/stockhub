@extends('supplier.layout.default')

@section('title', __('supplier.edit_product_tittle'))

@section('stylesheets')
<style type="text/css">
    .thumbnail{
        width: 100%;
        height: 190px;
        background-size: cover;
        background-position: center;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1 class="display-2 text-white">{{ $product->title }}</h1>
                <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">{{ __('supplier.back') }}</a>
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
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('supplier.product') }}</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('supplier.products.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">{{ __('supplier.product_info') }}</h6>
                        <div class="row justify-content-center">
                            <div class="col-lg-2 col-6">
                                <div class="d-flex  justify-content-center">
                                    <div class="image-hover">
                                    @if ($product->img_source != '')   
                                            <img id="product_img_source" src="{{$product->img_source}}" class="img-fluid" style="max-height:150px">
                                            @else 
                                            <img id="product_img_source" src="{{asset('assets/img/products/eng-product-no-image.png')}}" class="img-fluid" style="max-height:150px">
                                            
                                            @endif    
                                       <div class="middle">
                                        <button type="button" id="example_img_button" onclick="uploadProductImage()" class="btn btn-sm btn-primary">{{ __('supplier.change_image') }}</button>
                                            <input type="file" class="d-none" id="product_img" onchange="changeProductImage(this)" name="img_source">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-10">
                                <div class="row">
                                    @if(Auth::guard('admin')->id() != null)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="ignore_percentage_on_tax">{{ __('supplier.discount_percent') }}</label>
                                                <input type="text" id="ignore_percentage_on_tax" class="form-control form-control-alternative" name="ignore_percentage_on_tax" placeholder="{{ __('supplier.discount_percent') }}" value="{{ old('title', $product->ignore_percentage_on_tax) }}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="category">{{ __('supplier.category') }}</label>
                                            <select id="category" class="form-control form-control-alternative" name="category">
                                                <option value="">{{ __('supplier.without_category') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id}}" {{ (old('category', $product->category_id) == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_title">{{ __('supplier.tittle') }}</label>
                                            <input type="text" id="product_title" class="form-control form-control-alternative" name="title" placeholder="{{ __('supplier.product_tittle') }}" value="{{ old('title', $product->title) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">{{ trans('supplier.ncm') }}</label>
                                            <input type="text" id="product_ncm" class="form-control form-control-alternative" name="ncm" placeholder="{{ __('supplier.') }}" value="{{ old('ncm', $product->ncm) }}">
                                        </div>
                                    </div>
                                     <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">GTIN/EAN</label>
                                            <input type="text" id="product_ean_gtin" class="form-control form-control-alternative" name="ean_gtin" placeholder="{{ __('supplier.') }}" value="{{ old('ean_gtin', $product->ean_gtin) }}">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">{{ __('supplier.origin_products') }}</label>
                                            <select name="products_from" id="products_from" class="form-control form-control-alternative" required>
                                                <option value="BR" {{$product->products_from == 'BR' ? 'selected' : ''}}>Brasil</option>
                                                <option value="CN" {{$product->products_from == 'CN' ? 'selected' : ''}}>China</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">{{ __('supplier.coin') }}</label>
                                            <select name="currency" id="currency" class="form-control">
                                                <option value="R$" {{ $product->currency == 'R$' ? 'selected' : '' }}>R$</option>
                                                <option value="US$" {{ $product->currency == 'US$' ? 'selected' : '' }}>US$</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Isento ICMS</label>
                                            <select name="icms_exemption" id="icms_exemption" class="form-control">
                                                <option value="0" {{ $product->icms_exemption == '0' ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                                <option value="1" {{ $product->icms_exemption == '1' ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_description">{{ __('supplier.description') }}</label>
                                            <textarea id="product_description" class="form-control form-control-alternative" name="description" placeholder="{{ __('supplier.product_description') }}" required>{{ old('description', $product->description) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_hash">Hash</label>
                                            <input type="text" id="product_hash" class="form-control form-control-alternative" value="{{ $product->hash }}" readonly>
                                        </div>
                                    </div>
                                   
                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="checkbox" name="public" {{ (old('public') == 'on' || $product->public == 1) ? 'checked' : '' }}> {{ __('supplier.public_product') }} <sup><i class="fas fa-question-circle" tooltip="true" title="{{ __('supplier.public_products_will_be') }}"></i></sup>
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
                                        <h3 class="mb-0">{{ trans('supplier.atributos_obrigatorios') }}</h3>
                                    </div>                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                         <div class="pl-lg-12">
                         <h3 class="mb-0">{{ trans('supplier.produto_joia') }}</h3>   
                         <div class="row">
                            
                                <div class="col-2">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.joia') }}</label>
                                            <select name="joias" id="joias" class="form-control">
                                                <option value="0" {{ $product->joias == '0' ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                                <option value="1" {{ $product->joias == '1' ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.tipo') }}</label>
                                            <select name="atributo_joias" id="atributo_joias" class="form-control">
                                                <option value="4006364" {{ $product->atributo_joias == '4006364' ? 'selected' : '' }}>{{ trans('supplier.alpaca') }}</option>
                                                <option value="4837600" {{ $product->atributo_joias == '4837600' ? 'selected' : '' }}>{{ trans('supplier.aco') }}</option>
                                                <option value="4006367" {{ $product->atributo_joias == '4006367' ? 'selected' : '' }}>{{ trans('supplier.folheado_ouro') }}</option>
                                                <option value="4006368" {{ $product->atributo_joias == '4006368' ? 'selected' : '' }}>{{ trans('supplier.folheado_prata') }}</option>
                                                <option value="2431881" {{ $product->atributo_joias == '2431881' ? 'selected' : '' }}>{{ trans('supplier.madeira') }}</option>
                                                <option value="2481971" {{ $product->atributo_joias == '2481971' ? 'selected' : '' }}>{{ trans('supplier.ouro_amarelo') }}</option>
                                                <option value="2481973" {{ $product->atributo_joias == '2481973' ? 'selected' : '' }}>{{ trans('supplier.ouro_branco') }}</option>
                                                <option value="4006366" {{ $product->atributo_joias == '4006366' ? 'selected' : '' }}>{{ trans('supplier.ouro_platina') }}</option>
                                                <option value="4006365" {{ $product->atributo_joias == '4006365' ? 'selected' : '' }}>{{ trans('supplier.ouro_prata') }}</option>
                                                <option value="2481972" {{ $product->atributo_joias == '2481972' ? 'selected' : '' }}>{{ trans('supplier.ouro_rose') }}</option>
                                                <option value="2787783" {{ $product->atributo_joias == '2787783' ? 'selected' : '' }}>{{ trans('supplier.platina') }}</option>
                                                <option value="2748302" {{ $product->atributo_joias == '2748302' ? 'selected' : '' }}>{{ trans('supplier.plastico') }}</option>
                                                <option value="2481975" {{ $product->atributo_joias == '2481975' ? 'selected' : '' }}>{{ trans('supplier.prata') }}</option>
                                                <option value="2832463" {{ $product->atributo_joias == '2832463' ? 'selected' : '' }}>{{ trans('supplier.titanio') }}</option>
                                        
                                            </select>
                                        </div>
                                    </div>
                                        
                                  


                                </div>
                            </div>

                        <div class="pl-lg-12">
                         <h3 class="mb-0">{{ trans('supplier.produto_cabos_entradas') }}</h3>   
                         <div class="row">
                            
                                <div class="col-2">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.cabo') }}</label>
                                            <select name="conexao_cabo" id="conexao_cabo" class="form-control">
                                                <option value="0" {{ $product->conexao_cabo == '0' ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                                <option value="1" {{ $product->conexao_cabo == '1' ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.tipo') }}</label>
                                            <select name="tipo_entrada" id="tipo_entrada" class="form-control">
                                                <option value="4027342" {{ $product->tipo_entrada == '4027342' ? 'selected' : '' }}>{{ trans('supplier.db25') }}</option>
                                                <option value="2230456" {{ $product->tipo_entrada == '2230456' ? 'selected' : '' }}>{{ trans('supplier.usb_tipo_c') }}</option>
                                                <option value="2230458" {{ $product->tipo_entrada == '2230458' ? 'selected' : '' }}>{{ trans('supplier.conector_dock') }}</option>
                                                <option value="2230457" {{ $product->tipo_entrada == '2230457' ? 'selected' : '' }}>{{ trans('supplier.usb_otg') }}</option>
                                              
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pl-lg-12">
                         <h3 class="mb-0">{{ trans('supplier.produto_smartphone') }}</h3>   
                         <div class="row">
                            
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.smartphone') }}</label>
                                            <select name="smartphone" id="smartphone" class="form-control">
                                                <option value="0" {{ $product->smartphone == '0' ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                                <option value="1" {{ $product->smartphone == '1' ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.dual_sim') }}</label>
                                            <select name="atrib_phone_dualsim" id="atrib_phone_dualsim" class="form-control">
                                                <option value="242085" {{ $product->tipo_entrada == '242085' ? 'selected' : '' }}>{{ trans('supplier.sim') }}</option>
                                                <option value="242084" {{ $product->tipo_entrada == '242084' ? 'selected' : '' }}>{{ trans('supplier.nao') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.quantidade_memoria_interna') }}</label>
                                            <input type="text" id="atrib_qtd_menint" class="form-control form-control-alternative" name="atrib_qtd_menint" placeholder="{{ trans('supplier.quantidade_memoria_interna') }}" value="{{ old('atrib_qtd_menint', $product->atrib_qtd_menint) }}">
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.memoria_interna') }}</label>
                                            <select name="atrib_phone_men_int" id="atrib_phone_men_int" class="form-control">
                                                <option value="GB" {{ $product->atrib_phone_men_int == 'GB' ? 'selected' : '' }}>{{ trans('supplier.gb') }}</option>
                                                <option value="MB" {{ $product->atrib_phone_men_int == 'MB' ? 'selected' : '' }}>{{ trans('supplier.mb') }}</option>
                                                <option value="TB" {{ $product->atrib_phone_men_int == 'TB' ? 'selected' : '' }}>{{ trans('supplier.tb') }}</option>
                                                <option value="kB" {{ $product->atrib_phone_men_int == 'kB' ? 'selected' : '' }}>{{ trans('supplier.kb') }}</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-2">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.quantidade_memoria_ram') }}</label>
                                            <input type="text" id="atrib_qtd_ram" class="form-control form-control-alternative" name="atrib_qtd_ram" placeholder="{{ trans('supplier.memoria_ram') }}" value="{{ old('atrib_qtd_ram', $product->atrib_qtd_ram) }}">
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.memoria_ram') }}</label>
                                            <select name="atrib_phone_ram" id="atrib_phone_ram" class="form-control">
                                                <option value="GB" {{ $product->atrib_phone_ram == 'GB' ? 'selected' : '' }}>{{ trans('supplier.gb') }}</option>
                                                <option value="MB" {{ $product->atrib_phone_ram == 'MB' ? 'selected' : '' }}>{{ trans('supplier.mb') }}</option>
                                                <option value="kB" {{ $product->atrib_phone_ram == 'kB' ? 'selected' : '' }}>{{ trans('supplier.kb') }}</option>
                                           
                                            </select>
                                        </div>
                                    </div>

                                  

                                   

                                </div>
                            </div>


                            <div class="row">
                            
                            <div class="col-2">
                                    <div class="form-group">
                                       
                                    <label class="form-control-label">{{ trans('supplier.cor') }}</label>
                                            <select name="atrib_phone_cor" id="atrib_phone_cor" class="form-control">
                                                <option value="52055"  {{ $product->atrib_phone_cor == '52055' ? 'selected' : '' }}>{{ trans('supplier.branco') }}</option>
                                                <option value="52028" {{ $product->atrib_phone_cor == '52028' ? 'selected' : '' }}>{{ trans('supplier.azul') }}</option>
                                                <option value="51993" {{ $product->atrib_phone_cor == '51993' ? 'selected' : '' }}>{{ trans('supplier.vermelho') }}</option>
                                                <option value="283165" {{ $product->atrib_phone_cor == '283165' ? 'selected' : '' }}>{{ trans('supplier.cinza') }}</option>
                                                <option value="52049" {{ $product->atrib_phone_cor == '52049' ? 'selected' : '' }}>{{ trans('supplier.preto') }}</option>
                                                <option value="51994" {{ $product->atrib_phone_cor == '51994' ? 'selected' : '' }}>{{ trans('supplier.rosa') }}</option>
                                                <option value="52014" {{ $product->atrib_phone_cor == '52014' ? 'selected' : '' }}>{{ trans('supplier.verde') }}</option>
                                                <option value="283164" {{ $product->atrib_phone_cor == '283164' ? 'selected' : '' }}>{{ trans('supplier.dourado') }}</option>
                                                <option value="52019" {{ $product->atrib_phone_cor == '52019' ? 'selected' : '' }}>{{ trans('supplier.verde_escuro') }}</option>
                                                <option value="283160" {{ $product->atrib_phone_cor == '283160' ? 'selected' : '' }}>{{ trans('supplier.azul_turquesa') }}</option>
                                                <option value="52022" {{ $product->atrib_phone_cor == '52022' ? 'selected' : '' }}>{{ trans('supplier.agua') }}</option>
                                                <option value="283162" {{ $product->atrib_phone_cor == '283162' ? 'selected' : '' }}>{{ trans('supplier.indigo') }}</option>
                                                <option value="52036" {{ $product->atrib_phone_cor == '52036' ? 'selected' : '' }}>{{ trans('supplier.lavanda') }}</option>
                                                <option value="283163" {{ $product->atrib_phone_cor == '283163' ? 'selected' : '' }}>{{ trans('supplier.rosa_chiclete') }}</option>
                                                <option value="51998" {{ $product->atrib_phone_cor == '51998' ? 'selected' : '' }}>{{ trans('supplier.bordo') }}</option>
                                                <option value="52003" {{ $product->atrib_phone_cor == '52003' ? 'selected' : '' }}>{{ trans('supplier.nude') }}</option>
                                                <option value="283161" {{ $product->atrib_phone_cor == '283161' ? 'selected' : '' }}>{{ trans('supplier.azul_marinho') }}</option>
                                                <option value="52008" {{ $product->atrib_phone_cor == '52008' ? 'selected' : '' }}>{{ trans('supplier.creme') }}</option>
                                                <option value="52045" {{ $product->atrib_phone_cor == '52045' ? 'selected' : '' }}>{{ trans('supplier.rosa_palido') }}</option>
                                                <option value="283153" {{ $product->atrib_phone_cor == '283153' ? 'selected' : '' }}>{{ trans('supplier.palha') }}</option>
                                                <option value="283150" {{ $product->atrib_phone_cor == '283150' ? 'selected' : '' }}>{{ trans('supplier.laranja_claro') }}</option>
                                                <option value="52043" {{ $product->atrib_phone_cor == '52043' ? 'selected' : '' }}>{{ trans('supplier.rosa_claro') }}</option>
                                                <option value="283148" {{ $product->atrib_phone_cor == '283148' ? 'selected' : '' }}>{{ trans('supplier.coral_claro') }}</option>
                                                <option value="283149" {{ $product->atrib_phone_cor == '283149' ? 'selected' : '' }}>{{ trans('supplier.coral') }}</option>
                                                <option value="52021" {{ $product->atrib_phone_cor == '52021' ? 'selected' : '' }}>{{ trans('supplier.azul_celeste') }}</option>
                                                <option value="52031" {{ $product->atrib_phone_cor == '52031' ? 'selected' : '' }}>{{ trans('supplier.azul_aco') }}</option>
                                                <option value="283156" {{ $product->atrib_phone_cor == '283156' ? 'selected' : '' }}>{{ trans('supplier.caqui') }}</option>
                                                <option value="52001" {{ $product->atrib_phone_cor == '52001' ? 'selected' : '' }}>{{ trans('supplier.bege') }}</option>
                                                <option value="52035" {{ $product->atrib_phone_cor == '52035' ? 'selected' : '' }}>{{ trans('supplier.violeta') }}</option>
                                                <option value="283154" {{ $product->atrib_phone_cor == '283154' ? 'selected' : '' }}>{{ trans('supplier.marrom_claro') }}</option>
                                                <option value="283155" {{ $product->atrib_phone_cor == '283155' ? 'selected' : '' }}>{{ trans('supplier.marrom_escuro') }}</option>
                                              
                                            </select>



                                    </div>
                                </div>



                                <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-control-label">{{ trans('supplier.operadora') }}</label>
                                            <select name="atrib_phone_oper" id="atrib_phone_oper" class="form-control">
                                                <option value="298335" {{ $product->atrib_phone_oper == '298335' ? 'selected' : '' }}>{{ trans('supplier.desbloqueado') }}</option>
                                                <option value="298333" {{ $product->atrib_phone_oper == '298333' ? 'selected' : '' }}>{{ trans('supplier.claro') }}</option>
                                                <option value="303172" {{ $product->atrib_phone_oper == '303172' ? 'selected' : '' }}>{{ trans('supplier.nextel') }}</option>
                                                <option value="298334" {{ $product->atrib_phone_oper == '298334' ? 'selected' : '' }}>{{ trans('supplier.tim') }}</option>
                                                <option value="298331" {{ $product->atrib_phone_oper == '298331' ? 'selected' : '' }}>{{ trans('supplier.oi') }}</option>
                                                <option value="298332" {{ $product->atrib_phone_oper == '298332' ? 'selected' : '' }}>{{ trans('supplier.vivo') }}</option>
                                            


                                            </select>
                                        </div>
                                    </div>

                            </div>    


                            


                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ __('supplier.options') }}</h3>
                                    </div>
                                    <a href="#!" class="btn btn-sm btn-primary" onclick="add_option()"><i class="fas fa-plus mr-1"></i> {{ trans('supplier.adicionar') }} {{ __('supplier.options') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">{{ __('supplier.options') }}</h6>
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-12" id="options_container">
                                    @forelse($product->options as $option)
                                        <div class="form-group d-inline-block mr-1 option_class" style="max-width: 150px">
                                            <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                <input type="text" class="form-control form-control-alternative" placeholder="{{ __('supplier.option_name') }}" name="options[{{ $option->id }}]" onchange="option_change(this)" option_id="{{ $option->id }}" value="{{ $option->name }}" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-sm btn-danger" type="button" onclick="remove_option(this)"><i class="fas fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p>{{ __('supplier.no_option_register') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ __('supplier.variants') }}</h3>
                                    </div>
                                    <a href="#!" class="btn btn-sm btn-primary" onclick="add_variant()"><i class="fas fa-plus mr-1"></i> {{ trans('supplier.adicionar') }} {{ __('supplier.variants') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="variants_container">

                        @forelse($product->variants as $variant)
                        <div class="row justify-content-center align-items-center variant_div">
                            <div class="col-lg-2 col-6">
                                <div class="d-flex justify-content-center">
                                    <div class="image-hover">
                                        <img id="img_source_{{ $variant->id }}" src="{{($variant->img_source) ? $variant->img_source : asset('assets/img/products/eng-product-no-image.png') }}" class="img-fluid" style="max-height:150px">
                                        <div class="middle">
                                            <button type="button" onclick="uploadVariantImage({{ $variant->id }})" class="btn btn-sm btn-primary">{{ __('supplier.change_image') }}</button>
                                            <input type="file" class="d-none" id="img_{{ $variant->id }}" onchange="changeVariantImage(this)" variant_id="{{ $variant->id }}" name="variants[{{ $variant->id }}][img_source]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-10">
                                <a href="#!" id="unpublish_variant_{{ $variant->id }}" onclick="unpublish_variant({{ $variant->id }})" class="btn btn-success btn-sm {{ ($variant->published == 0) ? 'd-none' : '' }}" data-toggle='tooltip' data-placement="top" data-original-title="Ativar variação">{{ __('supplier.activate') }}</a>
                                <a href="#!" id="publish_variant_{{ $variant->id }}" onclick="publish_variant({{ $variant->id }})" class="btn btn-danger btn-sm {{ ($variant->published == 1) ? 'd-none' : '' }}" data-toggle='tooltip' data-placement="top" data-original-title="Desativar variação">{{ __('supplier.inactivate') }}</a>
                                <div class="float-right">
                                    <button class="btn btn-danger btn-sm" type="button" onclick="remove_variant(this)">{{ __('supplier.remove_variant') }}</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless variant-fields-table">
                                        <thead>
                                            <th>{{ trans('supplier.sku') }}</th>
                                            <th>{{ __('supplier.stock') }}</th>
                                            @foreach($product->options as $option)
                                                <th class="option_{{ $option->id }}_th">{{ $option->name }}</th>
                                            @endforeach
                                            <th class="after_option_th" variant_id="{{ $variant->id }}">{{ trans('supplier.money_price') }}</th>
                                            @if($authenticated_user->id == 56)
                                                <th>{{ __('supplier.factory_price') }}</th>
                                            @endif
                                            {{--<th>Custo</th>--}}
                                            <th>{{ __('supplier.weight') }}</th>
                                            <th>{{ __('supplier.width') }}</th>
                                            <th>{{ __('supplier.height') }}</th>
                                            <th>{{ __('supplier.depth') }}</th>
                                        </thead>
                                        <tbody>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][sku]" value="{{ $variant->sku }}" placeholder="{{ trans('supplier.sku') }}" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="number" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][stock]" value="{{ ($variant->stock) ? $variant->stock->quantity : 0 }}" placeholder="{{ __('supplier.stock_quantity') }}" required>
                                                </div>
                                            </td>

                                            @foreach($variant->options_values as $option_value)
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control form-control-alternative option_{{ $option_value->product_option_id }}_td" name="variants[{{ $variant->id }}][options][{{ $option_value->product_option_id }}]" placeholder="{{ ($option_value->option) ? $option_value->option->name : '' }}" value="{{ $option_value->value }}" required>
                                                    </div>
                                                </td>
                                            @endforeach

                                            <td class="after_option_td" variant_id="{{ $variant->id }}">
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">{{ $product->currency }}</span>
                                                        </div>
                                                        <input type="text" class="form-control form-control-alternative decimal" name="variants[{{ $variant->id }}][price]" placeholder="{{ __('supplier.price') }}" value="{{ $variant->price }}" required>
                                                    </div>
                                                </div>
                                            </td>
                                            @if($authenticated_user->id == 56)
                                                <td>
                                                    <div class="form-group">
                                                        <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">{{ $product->currency }}</span>
                                                            </div>
                                                            <input type="text" class="form-control form-control-alternative decimal" name="variants[{{ $variant->id }}][internal_cost]" placeholder="{{ __('supplier.factory_price') }}" value="{{ $variant->internal_cost }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                            {{--<td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">R$</span>
                                                        </div>
                                                        <input type="text" class="form-control form-control-alternative decimal" name="variants[{{ $variant->id }}][cost]" placeholder="Custo" value="{{ $variant->cost }}" required>
                                                    </div>
                                                </div>
                                            </td>--}}
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">g</span>
                                                        </div>
                                                    <input type="number" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][weight_in_grams]" placeholder="{{ __('supplier.weigth') }}" value="{{ $variant->weight_in_grams }}" required>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative decimal" name="variants[{{ $variant->id }}][width]" placeholder="{{ __('supplier_width') }}" value="{{ $variant->width }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative decimal" name="variants[{{ $variant->id }}][height]" placeholder="{{ __('supplier.height') }}" value="{{ $variant->height }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative decimal" name="variants[{{ $variant->id }}][depth]" placeholder="{{ __('supplier.depth') }}" value="{{ $variant->depth }}">
                                                    </div>
                                                </div>
                                            </td>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="my-4 w-100" />
                        </div>
                        @empty
                            <p>{{ __('supplier.no_variant_register') }}.</p>
                        @endforelse
                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ __('supplier.image_library') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <label for="files">{{ __('supplier.multiple_select_image') }}: </label>
                        <input type="file" class="form-control" id="files" name="images[]" accept="image/png, image/jpeg" multiple/>
                        <div class="row mt-4">
                            @foreach($product->images as $image)
                                <div class="d-flex col-lg-3 col-md-6 col-12 align-items-center justify-content-center" id="image-{{ $image->id }}">
                                    <div class="thumbnail" style="background-image: url('{{ $image->src  }}')">
                                        <button type="button" class="btn btn-sm btn-danger" style="position: absolute; top: 20px; right: 30px;" onclick="remove_image({{ $image->id }}, '{{ route('supplier.products.delete_image', $image->id) }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <output class="row mt-4" id="result" />
                    </div>
                    @if($authenticated_user->id == 56)
                        {{-- Caso seja igual ao id da S2M2 --}}
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <h3 class="mb-0">China Division</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <label>{{ __('supplier.shipping_method_china_division') }}: </label>
                            <select name="shipping_method_china_division" id="shipping_method_china_division" class='form-control'>
                                <option value="CDEUB" {{ $product->shipping_method_china_division == 'CDEUB' ? 'selected' : '' }}>CDEUB</option>
                                <option value="PUAM" {{ $product->shipping_method_china_division == 'PUAM' ? 'selected' : '' }}>PUAM</option>
                                <option value="SZEUB" {{ $product->shipping_method_china_division == 'SZEUB' ? 'selected' : '' }}>SZEUB</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <label>{{ __('supplier.packing_weight_china_division') }}: </label>
                            <div class='input-group input-group-alternative flex-nowrap mb-3'>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">g</span>
                                </div>
                                <input type="number" class="form-control form-control-alternative" name="packing_weight" placeholder="{{__('supplier.weight')}}" value="{{ $product->packing_weight ? $product->packing_weight : '10' }}" required="">
                            </div>
                        </div>

                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <h3 class="mb-0">{{ __('supplier.automatic_discounts') }}</h3>
                                        </div>
                                        <a href="#!" class="btn btn-sm btn-primary" onclick="add_discount()"><i class="fas fa-plus mr-1"></i> {{ trans('supplier.adicionar') }} {{ __('supplier.discount') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="discounts_container">
                            @forelse($product->discounts as $discount)
                                <div class='row new_discount'>
                                    <div class='col-lg-6'>
                                        <div class='table-responsive'>
                                            <table class='table table-borderless variant-fields-table'>
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('supplier.quantidade') }}</th>
                                                        <th>{{ trans('supplier.discount') }}(%)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>                                                    
                                                    <tr>
                                                        <td>
                                                            <div class='form-group'>
                                                                <input type="number" class='form-control form-control-alternative' name='new_discounts[{{$discount->id}}][quantity]' placeholder="{{ trans('supplier.quantidade') }}" value='{{$discount->quantity}}'>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class='form-group'>
                                                                <input type="number" step="0.01" class='form-control form-control-alternative' name='new_discounts[{{$discount->id}}][value]' placeholder="{{ trans('supplier.discount') }}" value='{{$discount->value}}'>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button class='btn btn-danger btn-sm' type='button' onclick="remove_discount(this)">{{__('supplier.remove_discount')}}</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @empty
                            
                            @endforelse
                        </div>
                    @endif
                    <div class="card-footer pb-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="float-right form-group">
                                    <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">{{ __('supplier.cancel') }}</a>
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

<div class="d-none">
    <input type="hidden" id="new_variants_count" value="0">
    <input type="hidden" id="new_options_count" value="0">
    <input type="hidden" id="new_discounts_count" value="0">

    <div class='row new_discount' id='discount_example'>
        <div class='col-lg-6'>
            <div class='table-responsive'>
                <table class='table table-borderless variant-fields-table'>
                    <thead>
                        <tr>
                            <th>{{ trans('supplier.quantidade') }}</th>
                            <th>{{ trans('supplier.discount') }}(%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class='form-group'>
                                    <input type="number" class='form-control form-control-alternative' name='new_discounts[0][quantity]' placeholder="{{ trans('supplier.quantidade') }}">
                                </div>
                            </td>
                            <td>
                                <div class='form-group'>
                                    <input type="number" step="0.01" class='form-control form-control-alternative' name='new_discounts[0][value]' placeholder="{{ trans('supplier.discount') }}">
                                </div>
                            </td>
                            <td>
                                <button class='btn btn-danger btn-sm' type='button' onclick="remove_discount(this)">{{__('supplier.remove_discount')}}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group d-inline-block mr-1" style="max-width: 150px" id="example_option">
        <div class="input-group input-group-alternative flex-nowrap mb-3">
            <input type="text" class="form-control form-control-alternative" placeholder="{{ __('supplier.option_name') }}" name="options[]" value="" required>
            <div class="input-group-append">
                <button class="btn btn-sm btn-danger" type="button" onclick="remove_option(this)"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>

    <div class="row justify-content-center align-items-center new_variant" id="example_variant">
        <div class="col-lg-2 col-6">
            <div class="d-flex justify-content-center">
                <div class="image-hover">
                    <img id="example_img_source" src="{{ asset('assets/img/products/eng-product-no-image.png') }}" class="img-fluid" style="max-height:150px">
                    <div class="middle">
                        <button type="button" id="example_img_button" onclick="uploadVariantImage(0)" class="btn btn-sm btn-primary">{{ __('supplier.change_image') }}</button>
                        <input type="file" class="d-none" id="example_img" onchange="changeVariantImage(this)" variant_id="0" name="new_variants[0][img_source]">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="float-right">
                <button class="btn btn-danger btn-sm" onclick="remove_variant(this)">{{ __('supplier.remove_variant') }}</button>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless variant-fields-table">
                    <thead>
                        <th>{{ trans('supplier.sku') }}</th>
                        <th>{{ __('supplier.stock') }}</th>
                        @foreach($product->options as $option)
                            <th class="option_{{ $option->id }}_th">{{ $option->name }}</th>
                        @endforeach
                        <th class="after_option_th" variant_id="0">{{ __('supplier.price') }}</th>
                        @if($authenticated_user->id == 56)
                            <th>{{ __('supplier.factory_price') }}</th>
                        @endif
                        {{--<th>Custo</th>--}}
                        <th>{{ __('supplier.weight') }}</th>
                        <th>{{ __('supplier.width') }}</th>
                        <th>{{ __('supplier.height') }}</th>
                        <th>{{ __('supplier.depth') }}</th>
                    </thead>
                    <tbody>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-alternative" name="new_variants[0][sku]" placeholder="{{ trans('supplier.sku') }}" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control form-control-alternative" name="new_variants[0][stock]" placeholder="{{ __('supplier.stock_quantity') }}" required>
                            </div>
                        </td>

                        @foreach($product->options as $option)
                            <td>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-alternative option_{{ $option->id }}_td" name="new_variants[0][options][{{ $option->id }}]" placeholder="{{ $option->name }}" required>
                                </div>
                            </td>
                        @endforeach

                        <td class="after_option_td" variant_id="0">
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ $product->currency }}</span>
                                    </div>
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][price]" placeholder="{{ __('supplier.money_price') }}" required>
                                </div>
                            </div>
                        </td>

                        @if($authenticated_user->id == 56)
                            <td>
                                <div class="form-group">
                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ $product->currency }}</span>
                                        </div>
                                        <input type="text" class="form-control form-control-alternative decimal" name="variants[0][internal_cost]" placeholder="{{ __('supplier.factory_price') }}">
                                    </div>
                                </div>
                            </td>
                        @endif

                        {{--<td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">R$</span>
                                    </div>
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][cost]" placeholder="Custo" required>
                                </div>
                            </div>
                        </td>--}}
                        <td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">g</span>
                                    </div>
                                    <input type="number" class="form-control form-control-alternative" name="new_variants[0][weight_in_grams]" placeholder="{{ __('supplier.weight') }}" required>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][width]" placeholder="{{ __('supplier.width') }}">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][height]" placeholder="{{ __('supplier.height') }}">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                    <input type="text" class="form-control form-control-alternative decimal" name="new_variants[0][depth]" placeholder="{{ __('supplier.depth') }}">
                                </div>
                            </div>
                        </td>
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-4 w-100" />
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>

<script type="text/javascript">
    //CKEDITOR.replace('product_description');

    window.onload = function(){
        //Check File API support
        if(window.File && window.FileList && window.FileReader)
        {
            var filesInput = document.getElementById("files");
            filesInput.addEventListener("change", function(event){
                var files = event.target.files; //FileList object
                var output = document.getElementById("result");

                output.innerHTML = '';

                for(var i = 0; i< files.length; i++)
                {
                    var file = files[i];
                    //Only pics
                    if(!file.type.match('image'))
                        continue;
                    var picReader = new FileReader();
                    picReader.addEventListener("load",function(event){
                        var picFile = event.target;
                        var div = document.createElement("div");
                        div.setAttribute('class', 'd-flex col-lg-3 col-md-6 col-12 align-items-center justify-content-center');

                        var thumb = document.createElement("div");
                        thumb.setAttribute('class', 'thumbnail');
                        thumb.style.backgroundImage = "url('"+ picFile.result.replace(/(\r\n|\n|\r)/gm, "") + "')";

                        div.innerHTML = thumb.outerHTML;

                        output.insertBefore(div,null);
                    });
                    //Read the image
                    picReader.readAsDataURL(file);
                }
            });
        }
        else
        {
            console.log("Browser not supported");
        }
    }

    function uploadVariantImage(variant_id){
        $('#img_'+variant_id).click()
    }

    function changeVariantImage(input) {
        if (input.files && input.files[0]) {
            var variant_id = $(input).attr('variant_id');

            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_source_'+variant_id)
                    .attr('src', e.target.result)
                    .width('auto')
                    .height(150);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function uploadProductImage(){
        $('#product_img').click();
    }

    function changeProductImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#product_img_source')
                    .attr('src', e.target.result)
                    .width('auto')
                    .height(150);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function option_change(option){
        var id = $(option).attr('option_id');

        $(".option_"+id+"_th").text($(option).val());
        $(".option_"+id+"_td").attr('placeholder', $(option).val());
    }

    function new_option_change(new_option){
        var id = $(new_option).attr('option_id');

        $(".new_option_"+id+"_th").text($(new_option).val());
        $(".new_option_"+id+"_td").attr('placeholder', $(new_option).val());
    }

    function add_option(){
        var option = $("#example_option").clone();
        var count = $("#new_options_count").val();

        if($(".option_class").length >= 3){
            $("#option-error").remove();
            $("#options_container").append('<div class="d-block text-danger" id="option-error">{{ __('supplier.you_cant_add_more_than_3_options') }}</div>');

            return false;
        }

        option.removeAttr('id');
        option.addClass('option_class');
        option.find('input:first').attr('name', 'new_options['+count+']');
        option.find('input:first').attr('option_id', count);
        option.find('input:first').attr('onchange', "new_option_change(this)");
        option.find('input:first').val('');
        option.find('button:first').attr('onclick', 'remove_new_option(this)');

        $("#options_container").append(option);

        append_variant_options(count);

        $("#new_options_count").val(++count);
    }

    function remove_option(button){
        var id = $(button).parent().parent().find('input:first').attr('option_id');

        $(".option_"+id+'_th').remove();
        $(".option_"+id+'_td').parent().parent().remove();

        $(button).parent().parent().parent().remove();
    }

    function remove_new_option(button){
        var id = $(button).parent().parent().find('input:first').attr('option_id');

        $(".new_option_"+id+'_th').remove();
        $(".new_option_"+id+'_td').parent().parent().remove();

        $(button).parent().parent().parent().remove();

        $("#option-error").remove();
    }

    function append_variant_options(count){
        $.each($('.after_option_th'), function(key, th){
            let variant_id = $(th).attr('variant_id');

            let new_option = '<th class="new_option_'+count+'_th"></th>';

            $(new_option).insertBefore(th);
        });

        $.each($('.after_option_td'), function(key, td){
            let variant_id = $(td).attr('variant_id');

            let new_option_name = '';

            if($(td).parents('.new_variant').length >= 1) {
                new_option_name = 'new_variants['+variant_id+'][new_options]['+count+']';
            }else{
                new_option_name = 'variants['+variant_id+'][new_options]['+count+']';
            }

            let new_option = '<td>';
            new_option += ' <div class="form-group">';
            new_option += '<input type="text" class="form-control form-control-alternative new_option_'+count+'_td" name="'+new_option_name+'" required>';
            new_option += '</div>';
            new_option += '</td>';

            $(new_option).insertBefore(td);
        });
    }

    function add_variant(){
        var variant = $("#example_variant").clone();
        var count = $("#new_variants_count").val();

        variant.removeAttr('id');

        $.each(variant.find('input'), function(key, input){
            let new_name = $(input).attr('name').replace('new_variants[0]', 'new_variants['+count+']');

            $(input).attr('name', new_name);
        });

        variant.find('.after_option_td').attr('variant_id', count);
        variant.find("#example_img_button:first").removeAttr('id').attr('onclick', 'uploadVariantImage('+count+')');
        variant.find('#example_img:first').attr('id', 'img_'+count).attr('variant_id', count);
        variant.find('#example_img_source:first').attr('id', 'img_source_'+count);

        $("#variants_container").append(variant);

        $("#new_variants_count").val(++count);

        $(".decimal").maskMoney({thousands:''});
    }

    function remove_variant(button){
        $(button).parent().parent().parent().remove();
    }

    function publish_variant(variant_id){
        $.ajax({
            url: '{{ url("supplier/products/".$product->id."/variants") }}/' + variant_id + '/publish',
            method: 'PUT',
            beforeSend: function(){
                $("#publish_variant_"+variant_id).html('<i class="fas fa-circle-notch fa-fw fa-spin"></i>');
            },
            success: function(response){
                $("#publish_variant_"+variant_id).addClass('d-none');
                $("#unpublish_variant_"+variant_id).removeClass('d-none');
            },
            complete: function(){
                $("#publish_variant_"+variant_id).html('Inactive');
            },
            error: function(response){
                //console.log(response);
            }
        });
    }

    function unpublish_variant(variant_id){
        $.ajax({
            url: '{{ url("supplier/products/".$product->id."/variants") }}/' + variant_id + '/unpublish',
            method: 'PUT',
            beforeSend: function(){
                $("#unpublish_variant_"+variant_id).html('<i class="fas fa-circle-notch fa-fw fa-spin"></i>');
            },
            success: function(response){
                $("#publish_variant_"+variant_id).removeClass('d-none');
                $("#unpublish_variant_"+variant_id).addClass('d-none');
            },
            complete: function(){
                $("#unpublish_variant_"+variant_id).html('Active');
            },
            error: function(response){
                //console.log(response);
            }
        });
    }

    function remove_image(image_id, route){
        $("#image-"+image_id).remove();

        $.ajax({
            url: route,
            method: 'DELETE'
        });
    }

    function add_discount(){
        var discount = $("#discount_example").clone();
        var countDiscount = $("#new_discounts_count").val();

        discount.removeAttr('id');

        $.each(discount.find('input'), function(key, input){
            let new_name = $(input).attr('name').replace('new_discounts[0]', 'new_discounts['+countDiscount+']');
            $(input).attr('name', new_name);
        });

        $("#discounts_container").append(discount);

        $("#new_discounts_count").val(++countDiscount);
    }

    function remove_discount(button){
        var countDiscount = $("#new_discounts_count").val();
        countDiscount--;
        $("#new_discounts_count").val(countDiscount);
        $(button).parent().parent().parent().parent().remove();
    }
</script>
@endsection
