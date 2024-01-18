@extends('supplier.layout.default')

@section('title', 'Configurações - Descontos')

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
            <div class="col-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Cadastrar cupom</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <form method="POST" action="{{ route('supplier.settings.discounts.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-lg-3 col-12">
                                    <div class="form-group">
                                        <label>% desconto</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            </div>
                                            <input type="number" name="percentage" id="percentage" placeholder="Porcentagem" class="form-control" min="0" max="100" value="" step=".01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="form-group">
                                        <label>Código</label>
                                        <input type="text" name="code" id="code" placeholder="Código" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="form-group">
                                        <label>Produto (variante)</label>
                                        <select name="variant_id" id="variant_id" class="form-control">
                                            @forelse($variants as $var)
                                                <option value="{{ $var->id }}">{{ $var->product->title }} - {{ $var->title }}</option>
                                            @empty
                                                <option value="">Nenhuma variante cadastrada</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="form-group">
                                        <label class="d-block">&nbsp;</label>
                                        <button class="btn btn-success btn-block pull-right"><i class="fas fa-check"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Cupons cadastrados</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Porcentagem</th>
                                        <th>Variante</th>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($discounts as $discount)
                                        <tr>
                                            <td>{{ $discount->code }}</td>
                                            <td>{{ $discount->percentage }}</td>
                                            <td>{{ $discount->variant && $discount->variant->product ? $discount->variant->product->title.' - '.$discount->variant->title : "Produto removido" }}</td>
                                            <td>
                                                <a class="btn btn-danger btn-sm" href="{{ route('supplier.settings.discounts.delete', [$discount->id]) }}"><i class="fas fa-fw fa-times"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Nenhum cupom cadastrado</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
