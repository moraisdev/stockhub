@extends('supplier.layout.default')

@section('title', __('supplier.see_product_tittle'))

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
                <div class="card-body">
                    <h6 class="heading-small text-muted mb-4">{{ __('supplier.product_info') }}</h6>
                    <div class="row justify-content-center">
                        <div class="col-lg-2 col-6">
                            <div class="d-flex  justify-content-center">
                            @if ($product->img_destaque != '')   
                                            <img id="product_img_source" src="{{$product->img_destaque}}" class="img-fluid" style="max-height:150px">
                                            @else 
                                            <img id="product_img_source" src="{{asset('assets/img/products/eng-product-no-image.png')}}" class="img-fluid" style="max-height:150px">
                                            
                                            @endif 
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-4 col-12">
                                    <div class="form-group">
                                        <label class="form-control-label" for="category">{{ __('supplier.category') }}</label>
                                        <select id="category" class="form-control form-control-alternative" name="category" disabled>
                                            <option>{{ ($product->category) ? $product->category->name : __('supplier.without_category') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-12">
                                    <div class="form-group">
                                    <label class="form-control-label" for="product_title">{{ __('supplier.tittle') }}</label>
                                    <input type="text" id="product_title" class="form-control form-control-alternative" name="title" placeholder="{{ __('supplier.product_tittle') }}" value="{{ old('title', $product->title) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="form-control-label" for="product_ncm">NCM</label>
                                        <input type="text" id="product_ncm" class="form-control form-control-alternative" name="ncm" placeholder="Nomenclatura Comum do Mercosul (NCM)" value="{{ old('ncm', $product->ncm) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="form-control-label" for="product_ncm">EAN/GTIN</label>
                                        <input type="text" id="product_ncm" class="form-control form-control-alternative" name="ean_gtin" placeholder="SEM GTIN ou EAN" value="{{ old('ean_gtin', $product->ean_gtin) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="form-control-label" for="product_ncm">{{ __('supplier.origin_products') }}</label>
                                        <select name="products_from" id="products_from" class="form-control form-control-alternative" disabled>
                                            <option value="BR" {{$product->products_from == 'BR' ? 'selected' : ''}}>Brasil</option>
                                            <option value="CN" {{$product->products_from == 'CN' ? 'selected' : ''}}>China</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="product_ncm">{{ __('supplier.coin') }}</label>
                                        <select name="currency" id="currency" class="form-control" disabled>
                                            <option value="R$" {{ $product->currency == 'R$' ? 'selected' : '' }}>R$</option>
                                            <option value="US$" {{ $product->currency == 'US$' ? 'selected' : '' }}>US$</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Isento ICMS</label>
                                        <select name="currency" id="icms_exemption" class="form-control" disabled>
                                            <option value="0" {{ $product->icms_exemption == '0' ? 'selected' : '' }}>Não</option>
                                            <option value="1" {{ $product->icms_exemption == '1' ? 'selected' : '' }}>Sim</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-control-label" for="product_description">{{ __('supplier.description') }}</label>
                                        <textarea id="product_description" class="form-control form-control-alternative" name="description" placeholder="{{ __('supplier.product_description') }}" readonly>{{ old('description', $product->description) }}</textarea>
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
                                    <input type="checkbox" name="public" {{ (old('public') == 'on' || $product->public == 1) ? 'checked' : '' }} disabled> {{ __('supplier.public_product') }} <sup><i class="fas fa-question-circle" tooltip="true" title="{{ __('supplier.public_products_will_be') }}"></i></sup>
                                    </div>
                                </div>
                                @if(env('PARTICULAR') == 0)
                                <div class="col-12">
                                    <div class="form-group">
                                    <input type="checkbox" name="show_in_products_page" {{ (old('show_in_products_page') == 'on' || $product->show_in_products_page == 1) ? 'checked' : '' }} disabled> {{ __('supplier.show_my_product_page') }} <sup><i class="fas fa-question-circle" tooltip="true" title="{{ __('supplier.show_product_in_private') }}"></i></sup>
                                    </div>
                                </div>
                                @endif
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
                                    <div class="form-group d-inline-block mr-1" style="max-width: 150px">
                                        <div class="input-group input-group-alternative flex-nowrap mb-3">
                                            <input type="text" class="form-control form-control-alternative" placeholder="{{ __('supplier.option_name') }}" name="options[{{ $option->id }}]" onchange="option_change(this)" option_id="{{ $option->id }}" value="{{ $option->name }}" readonly>
                                        </div>
                                    </div>
                                @empty
                                    <p>{{ __('supplier.no_option_product') }}.</p>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="variants_container">

                    @forelse($product->variants as $variant)
                    <div class="row justify-content-center align-items-center variant_div">
                        <div class="col-lg-2 col-6">
                            <div class="d-flex justify-content-center">
                                <img id="img_source_{{ $variant->id }}" src="{{ (/imgproduto/'.$variant->img_source) ? $variant->img_source : asset('assets/img/products/eng-product-no-image.png') }}" class="img-fluid" style="max-height:150px">
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <div class="table-responsive">
                                <table class="table table-borderless variant-fields-table">
                                    <thead>
                                        <th>SKU</th>
                                        <th>{{ __('supplier.stock') }}</th>
                                        @foreach($product->options as $option)
                                            <th class="option_{{ $option->id }}_th">{{ $option->name }}</th>
                                        @endforeach
                                        <th class="after_option_th" variant_id="{{ $variant->id }}">{{ __('supplier.price') }}</th>
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
                                                <input type="text" class="form-control form-control-alternative" name="new_variants[0][sku]" placeholder="SKU" value="{{ $variant->sku }}" readonly>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-alternative" name="new_variants[0][stock]" placeholder="Estoque" value="{{ ($variant->stock) ? $variant->stock->quantity : 0 }}" readonly>
                                            </div>
                                        </td>

                                        @foreach($variant->options_values as $option_value)
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control form-control-alternative option_{{ $option_value->product_option_id }}_td" name="variants[{{ $variant->id }}][options][{{ $option_value->product_option_id }}]" placeholder="{{ ($option_value->option) ? $option_value->option->name : '' }}" value="{{ $option_value->value }}" readonly>
                                                </div>
                                            </td>
                                        @endforeach

                                        <td class="after_option_td" variant_id="{{ $variant->id }}">
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-secondary">{{ $product->currency }}</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-alternative decimal pl-2" name="variants[{{ $variant->id }}][price]" placeholder="{{ __('supplier.price') }}" value="{{ $variant->price }}" readonly>
                                                </div>
                                            </div>
                                        </td>

                                        @if($authenticated_user->id == 56)
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-secondary">{{ $product->currency }}</span>
                                                        </div>
                                                        <input type="text" class="form-control form-control-alternative decimal pl-2" name="variants[{{ $variant->id }}][internal_cost]" placeholder="{{ __('supplier.factory_price') }}" value="{{ $variant->internal_cost }}" readonly>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif

                                        {{--<td>
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-secondary">R$</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-alternative decimal pl-2" name="variants[{{ $variant->id }}][cost]" placeholder="Custo" value="{{ $variant->cost }}" readonly>
                                                </div>
                                            </div>
                                        </td>--}}

                                        <td>
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-secondary">g</span>
                                                    </div>
                                                    <input type="number" class="form-control form-control-alternative pl-2" name="variants[{{ $variant->id }}][weight_in_grams]" placeholder="{{ __('supplier.wigth') }}" value="{{ $variant->weight_in_grams }}" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                    <input type="text" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][width]" placeholder="{{ __('supplier.width') }}" value="{{ $variant->width }}" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                    <input type="text" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][height]" placeholder="{{ __('supplier.height') }}" value="{{ $variant->height }}" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                <input type="text" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][depth]" placeholder="{{ __('supplier.depth') }}" value="{{ $variant->depth }}" readonly>
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
                    <div class="row mt-4">
                        @forelse($product->images as $image)
                            <div class="d-flex col-lg-3 col-md-6 col-12 align-items-center justify-content-center">
                                <div class="thumbnail" style="background-image: '{{$image->src}}'"></div>
                            </div>
                        @empty
                            <div class="col">
                                {{ __('supplier.no_image_library_register') }}.
                            </div>
                        @endforelse
                    </div>
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
                        <label for="files">{{ __('supplier.shipping_method_china_division') }}: </label>
                        <select name="shipping_method_china_division" id="shipping_method_china_division" class='form-control form-control-alternative' disabled>
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
                            <input type="number" class="form-control form-control-alternative" name="packing_weight" placeholder="{{__('supplier.weight')}}" value="{{ $product->packing_weight ? $product->packing_weight : '10' }}" disabled>
                        </div>
                    </div>

                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ __('supplier.automatic_discounts') }}</h3>
                                    </div>
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
                                                    <th>Quantidade</th>
                                                    <th>Desconto(%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>                                            
                                                <tr>
                                                    <td>
                                                        <div class='form-group'>
                                                            <input type="number" class='form-control form-control-alternative' name='new_discounts[0][quantity]' placeholder="Quantidade" value='{{$discount->quantity}}' disabled>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class='form-group'>
                                                            <input type="number" step="0.01" class='form-control form-control-alternative' name='new_discounts[0][value]' placeholder="Desconto" value='{{$discount->value}}' disabled>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p></p>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
