@extends('shop.layout.default')

@section('title', config('app.name').' - Detalhes do fornecedor')

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Paid orders</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-cart-plus"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total balance</h5>
                                    <span class="h2 font-weight-bold mb-0">$0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Profit</h5>
                                    <span class="h2 font-weight-bold mb-0">$0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Profit percentage</h5>
                                    <span class="h2 font-weight-bold mb-0">0%</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-percent"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
    	<div class="col-12 mb-3">
    		<div class="card shadow">
    			<div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">{{ $supplier->name }}</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <b>Documento de identificação:</b> {{ $supplier->document }} <br>
                        <b>Data de cadastro no {{ config('app.name') }}:</b> {{ date('d/m/Y', strtotime($supplier->created_at)) }}
                    </p>
                </div>
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-0">Produtos deste fornecedor na minha loja</h2>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Vendas na minha loja</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($authenticated_user->supplier_products($supplier->id) as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->title }}</td>
                                    <td>0</td>
                                    <td>
                                        <a href="{{ route('shop.products.show', $product->id) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes">
                                            <i class="fas fa-fw fa-info"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Nenhum fornecedor fornecendo produtos à sua loja.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
    		</div>
    	</div>
    </div>
</div>
@endsection
