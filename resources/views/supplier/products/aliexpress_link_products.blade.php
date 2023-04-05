@extends('supplier.layout.default')

@section('title', __('supplier.products'))

@section('stylesheets')
<style type="text/css"> 
    .btn-circle {  
        padding: 7px 10px; 
        border-radius: 50%; 
        font-size: 1rem; 
    } 
</style> 
@endsection

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12 mb-5 mb-xl-0 mb-3">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ trans('supplier.variante') }}</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('supplier.products.aliexpress.update_variants_skus', [$product->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="tab-content" id="pills-tabContent">
                        @php
                            $total_count = count($product->variants);
                            $progress = 100 / $total_count;
                            $increase = 100 / $total_count;
                        @endphp
                        @forelse($product->variants as $variant)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-variant-{{ $loop->index }}" role="tabpanel">
                            <div class="card-body">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped bg-primary" style="width:{{ $progress }}%"></div>
                                </div>
                                <div class="row justify-content-center align-items-center variant_div">
                                    <div class="col-lg-2 col-6">
                                        <div class="d-flex justify-content-center">
                                            <img id="img_source_{{ $variant->id }}" src="{{ ($variant->img_source) ? $variant->img_source : asset('assets/img/products/eng-product-no-image.png') }}" class="img-fluid" style="max-height:150px">
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <h3>{{ $product->title }}</h3>
                                        <div class="table-responsive">
                                            <table class="table table-borderless variant-fields-table">
                                                <thead>
                                                    @foreach($product->options as $option)
                                                        <th class="option_{{ $option->id }}_th">{{ $option->name }}</th>
                                                    @endforeach
                                                </thead>
                                                <tbody>
                                                    @foreach($variant->options_values as $option_value)
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control form-control-alternative option_{{ $option_value->product_option_id }}_td" name="variants[{{ $variant->id }}][options][{{ $option_value->product_option_id }}]" placeholder="{{ ($option_value->option) ? $option_value->option->name : '' }}" value="{{ $option_value->value }}" readonly>
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4 w-100" />
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{ trans('supplier.selecione_aliexpress') }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center flex-wrap">
                                    @if(!$loop->first)
                                    <div class="form-group ml-2 mr-2">
                                        <label class="control-label d-block">&nbsp;</label>
                                        <button type="submit" class="btn btn-secondary mr-2" data-toggle="tab" href="#tab-variant-{{ $loop->index - 1 }}" role="tab">{{ trans('supplier.back') }}</button>
                                    </div>
                                    @endif
                                    @php 
                                    $count = 0;
                                    @endphp
                                    @foreach($ae_product->options as $option)
                                        <div class="form-group ml-2 mr-2">
                                            <label class="control-label">{{ $option->option }}</label>
                                            <select class="form-control" name="options[{{ $variant->id }}][{{ $count }}]">
                                                @foreach(explode(',', $option->values) as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @php 
                                        $count++;
                                        @endphp
                                    @endforeach
                                    <div class="form-group ml-2 mr-2">
                                        @if(!$loop->last)
                                        <div class="form-group ml-2 mr-2">
                                            <label class="control-label d-block">&nbsp;</label>
                                            <button type="button" class="btn btn-primary" data-toggle="tab" href="#tab-variant-{{ $loop->index + 1 }}" role="tab">{{ trans('supplier.proxima_variante') }}</button>
                                        </div>
                                        @else
                                        <div class="form-group ml-2 mr-2">
                                            <label class="control-label d-block">&nbsp;</label>
                                            <button type="submit" class="btn btn-success">{{ trans('supplier.concluir_ligacao_variantes') }}</button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $progress += $increase;
                        @endphp
                        @empty
                            <p>{{ trans('supplier.nenhum_variante_cadastrada') }}</p>
                        @endforelse
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $('button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $(e.target).removeClass('active');
    })
</script>
@endsection