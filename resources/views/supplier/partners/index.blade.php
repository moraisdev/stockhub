@extends('supplier.layout.default')

@section('title', __('supplier.partners_tittle'))

@section('content')
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <!--<div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Dash data</h5>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Dash data</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Dash data</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
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
                                    <h5 class="card-title text-uppercase text-muted mb-0">Dash data</h5>
                                    <span class="h2 font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-percent"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>-->
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
                        <h2 class="mb-0">{{ __('supplier.my_partners') }}</h2>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                    <table class="table table-flush align-items-center">
                        <thead>
                            <tr>
                                <th>{{ __('supplier.name_store') }}</th>
                                <th>{{ __('supplier.linked_to_me') }}</th>
                                <th>{{ __('supplier.details') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($authenticated_user->shops as $shop)
                                <tr>
                                    <td>{{ $shop->name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($shop->pivot->date)) }}</td>
                                    <td>
                                        <a href="{{ route('supplier.partners.show', $shop->hash) }}" class="btn btn-primary btn-sm" tooltip="true" title="Detalhes da loja">
                                            <i class="fas fa-fw fa-info"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">{{ __('supplier.dont_have_linked_store') }}.</td>
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
