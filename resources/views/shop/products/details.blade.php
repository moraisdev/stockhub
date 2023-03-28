@extends('shop.layout.default')

@section('title', config('app.name').' - Visualizar produto')

@section('stylesheets')
    <style type="text/css">
        .thumbnail{
            width: 100%;
            height: 250px;
            background-size: cover;
            background-position: center;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <!-- Mask -->
        <span class="mask bg-gradient-default opacity-8"></span>
        <!-- Header container -->
        <div class="container-fluid d-flex align-items-center">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <h1 class="display-4 text-white">{{ $product->title }}</h1>
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
                            <div class="col-xl">
                                <h3 class="mb-0">Produto</h3>
                            </div>
                            @if($product->hash)
                                <div class="col-xl-3">
                                    <a href="{{route('shop.products.link_private', ['hash' => $product->hash ])}}" class="btn btn-sm btn-success btn-block mt-4">Importar para produtos</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">Informações do produto</h6>
                        <div class="row justify-content-center align-items-center">
                            <div class="col-lg-2 col-6">
                                <div class="d-flex  justify-content-center">
                                @if ($product->img_source != '')   
                                            <img id="img_source_{{ $product->id }}" src="{{$product->img_source}}" class="img-fluid" style="max-height:50px">
                                            @else 
                                            <img id="img_source_{{ $product->id }}" src="{{asset('assets/img/products/eng-product-no-image.png')}}" class="img-fluid" style="max-height:50px">
                                               
                            
                                @endif
                            
                            </div>
                            </div>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="category">Categoria</label>
                                            <select id="category" class="form-control form-control-alternative" name="category" disabled>
                                                <option>{{ ($product->category) ? $product->category->name : 'Sem Categoria' }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_title">Título</label>
                                            <input type="text" id="product_title" class="form-control form-control-alternative" name="title" placeholder="Título do Produto" value="{{ old('title', $product->title) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">NCM</label>
                                            <input type="text" id="product_ncm" class="form-control form-control-alternative" name="ncm" placeholder="Nomenclatura Comum do Mercosul (NCM)" value="{{ old('ncm', $product->ncm) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">{{ __('supplier.origin_products') }}</label>
                                            <select name="products_from" id="products_from" class="form-control form-control-alternative" disabled>
                                                <option value="BR" {{$product->products_from == 'BR' ? 'selected' : ''}}>Brasil</option>
                                                <option value="CN" {{$product->products_from == 'CN' ? 'selected' : ''}}>China</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_description">Descrição</label>
                                            <textarea id="product_description" class="form-control form-control-alternative" name="description" placeholder="Description" readonly>{{ old('description', $product->description) }}</textarea>
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
                                        <h3 class="mb-0">Opções</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">Opções</h6>
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-12" id="options_container">
                                    @forelse($product->options as $option)
                                        <div class="form-group d-inline-block mr-1" style="max-width: 150px">
                                            <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                <input type="text" class="form-control form-control-alternative" placeholder="Option name" name="options[{{ $option->id }}]" onchange="option_change(this)" option_id="{{ $option->id }}" value="{{ $option->name }}" readonly>
                                            </div>
                                        </div>
                                    @empty
                                        <p>Nenhuma opção cadastrada.</p>
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
                                        <h3 class="mb-0">Variantes</h3>
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
                                        <img id="img_source_{{ $variant->id }}" src="{{ ($variant->img_source) ? $variant->img_source : asset('assets/img/products/product-no-image.png') }}" class="img-fluid" style="max-height:150px">
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="table-responsive">
                                        <table class="table table-borderless variant-fields-table">
                                            <thead>
                                            @foreach($product->options as $option)
                                                <th class="option_{{ $option->id }}_th">{{ $option->name }}</th>
                                            @endforeach
                                            <th class="after_option_th" variant_id="{{ $variant->id }}">Preço ({{ $product->currency }})</th>
                                            <th>Peso (g)</th>
                                            <th>Largura (cm)</th>
                                            <th>Altura (cm)</th>
                                            <th>Profundidade (cm)</th>
                                            </thead>
                                            <tbody>

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
                                                            <span class="input-group-text bg-secondary">$</span>
                                                        </div>
                                                        <input type="text" class="form-control form-control-alternative decimal pl-2" name="variants[{{ $variant->id }}][price]" placeholder="Preço" value="{{ $variant->price }}" readonly>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-secondary">g</span>
                                                        </div>
                                                        <input type="number" class="form-control form-control-alternative pl-2" name="variants[{{ $variant->id }}][weight_in_grams]" placeholder="Peso (g)" value="{{ $variant->weight_in_grams }}" readonly>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][width]" placeholder="Largura (cm)" value="{{ $variant->width }}" readonly>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][height]" placeholder="Altura (cm)" value="{{ $variant->height }}" readonly>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group input-group-alternative flex-nowrap mb-3">
                                                        <input type="text" class="form-control form-control-alternative" name="variants[{{ $variant->id }}][depth]" placeholder="Profundidade (cm)" value="{{ $variant->depth }}" readonly>
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
                            <p>Nenhuma variante cadastrada.</p>
                        @endforelse
                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">Biblioteca de Imagens</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mt-4">
                            @forelse($product->images as $image)
                                <div class="d-flex col-lg-3 col-md-6 col-12 align-items-center justify-content-center">
                                    <div class="thumbnail" style="background-image: url('{{ $image->src  }}')"></div>
                                </div>
                            @empty
                                <div class="col">
                                    Nenhuma imagem cadastrada ainda.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
