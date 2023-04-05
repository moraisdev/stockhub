@extends('admin.layout.default')

@section('title', config('app.name'))

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ trans('supplier.media_ganhos') }}</h5>
                                    <span class="h2 font-weight-bold text-success mb-0">R$ {{ number_format($totalAmountMedia, 2, ',','.') }} {{ trans('supplier.semanal') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ trans('supplier.week_earnings') }}</h5>
                                    <span class="h2 font-weight-bold mb-0">R$ {{ number_format($totalAmountWeek, 2, ',','.') }}</span>
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
                <div class="col-xl-4 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ trans('supplier.total_earnings') }}</h5>
                                    <span class="h2 font-weight-bold mb-0">
                                    R$ {{ number_format($totalAmount, 2, ',','.') }}
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
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
                            <h2 class="mb-0">{{ trans('supplier.painel_administrativo') }}</h2>
                        </div>
                    </div>
                </div>
    			<div class="card-body">
                {{ trans('supplier.bem_vindo_painel_administrativo') }} {{config('app.name')}}!
    			</div>
    		</div>
    	</div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $("#link").on('click', function(){
        $("#link").select();
        document.execCommand('copy');

        $('#link_copy_alert').fadeIn();
        window.setTimeout(function(){
            $('#link_copy_alert').fadeOut();
        }, 1000);
    })

</script>
@endsection