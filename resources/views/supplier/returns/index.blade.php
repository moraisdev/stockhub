@extends('supplier.layout.default')

@section('title', __('supplier.reembolsos_title'))

@section('content')
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8"></div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">{{ trans('supplier.reembolsos_title') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center">
                            <p>
                            {{ trans('supplier.text_listagem_pedidos_reembolso') }}
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                            <tr>
                                <th>{{ trans('supplier.date') }}</th>
                                <th>{{ trans('supplier.pedido') }}</th>
                                <th>{{ trans('supplier.text_status') }}</th>
                                <th>{{ trans('supplier.novas_mensagens') }}</th>
                                <th style="width: 50px" class="text-center">{{ trans('supplier.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $return)
                                    <tr>
                                        <td>{{ $return->created_at->format('d/m/Y') }}</td>
                                        <td><a href="{{ route('supplier.orders.show', $return->supplier_order_id) }}" target=")blank">{{ $return->supplier_order->f_display_id }}</a></td>
                                        <td>{{ $return->f_status }}</td>
                                        <td>{{ $return->messages->where('supplier_id', null)->where('read', 0)->count() }} {{ trans('supplier.novas_mensagens') }}.</td>
                                        <td class="text-center">
                                            <a href="{{ route('supplier.returns.show', $return->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">{{ trans('supplier.text_reembolso_nao_disponivel') }}</td>
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
