@extends('shop.layout.default')

@section('title', config('app.name').' - '.trans('supplier.fornecedores'))

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">

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
                            <h2 class="mb-0">{{ trans('supplier.meus_fornecedores') }}</h2>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th>{{ trans('supplier.name') }}</th>
                                <th>Adicionado em</th>
                                <th>{{ trans('supplier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($authenticated_user->suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($supplier->pivot->date)) }}</td>
                                    <td>
                                        <a href="{{ route('shop.partners.show', $supplier->hash) }}" class="btn btn-primary btn-sm" tooltip="true" title="Visualizar produtos">
                                            <i class="fas fa-fw fa-boxes"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Nenhum fornecedor ligado à sua loja no momento.</td>
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
