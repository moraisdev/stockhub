@extends('shop.layout.default')

@section('title', 'Plano de Assinatura - Faturas')

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
            <div class="col-md-12 mb-5">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Plano Contratado</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                        @if($shop_invoice)
                            
                                     @if ($shop_invoice->name_plan == 'FREE') 
                                     <h3>Plano Gratuito</h3> 
                                     @else
                                    <h3>{{$shop_invoice->name_plan}}</h3>
                                     @endif
                                     <p>
                                     @if ($shop_invoice->subscription_status == 'active')
                                     {{ trans('supplier.situacao') }}: <b>{{ trans('supplier.ativa') }}</b><br>
                                     @else
                                     {{ trans('supplier.situacao') }}: <b>{{ trans('supplier.inativa') }}</b><br>
                                     @endif
                                     {{ trans('supplier.price') }}: <b>R$ {{number_format($shop_invoice->valor,2,",",".")}}</b>
                                        <br>
                                        Método de pagamento: <b></b>
                                        <br>
                                        Vencimento: <b>{{date('d/m/Y', strtotime($shop_invoice->due_date))}}</b>
                                    </p>

                                     
                        @endif
                        </div>
                    </div>
                    <div class="form-group text-center">
                                        <a href='{{route('shop.plans.index')}}' class="btn btn-primary mb-3" style="color: #fff; margin-right: 0;">Alterar Plano</a>
                                    </div>
                    
                 
                </div>
            </div>
        </div>
    </div>

    <br>
    <br>
    <br>

    <div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Faturas Geradas</h3>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body my-0">
                    <p class="my-0">Listagem de faturas geradas pelo sistema.</p>
                </div>
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Plano</th>
                                <th scope="col">Vencimento</th>
                                <th scope="col">{{ trans('supplier.price') }}</th>
                                <th scope="col">Data Pagamento</th>
                                <th scope="col">{{ trans('supplier.text_status') }}</th>
                                <th scope="col" class="actions-th">{{ trans('supplier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shop_invoice_all as $invoice)
                            
                            <tr>
                                <th scope="row">
                                    #{{ $invoice->id }}
                                </th>
                                <td>
                                @if ($invoice->plan == 'FREE' )     
                                {{ trans('supplier.gratuito') }}
                                @else
                                {{ $invoice->plan }}
                                @endif
                                </td>
                                <td>
                                    {{ date('d/m/Y', strtotime($invoice->due_date)) }}
                                </td>
                               
                                <td>
                                R$ {{number_format($invoice->total,2,",",".")}}
                                </td>
                                
                                <td>
                                @if ($invoice->date_payment != null)
                                    {{ date('d/m/Y', strtotime($invoice->date_payment)) }}
                                @endif    
                                </td>

                              
                                <td>
                                    @if ($invoice->payment == 'paid' )
                                         Pago
                                    @else 
                                    {{ trans('supplier.pendente') }}
                                    @endif
                                   
                                </td>


                                <td>
                                    @if ($invoice->payment == 'paid' ) 
                                    <a href="{{ route('shop.plans.pay', $invoice->id) }}" class="btn btn-primary" role='button'>Detalhe</a>
                                  
                                    @else 
                                    <a href="{{ route('shop.plans.pay', $invoice->id) }}" class="btn btn-success" role='button'>Pagar</a>
                                  
                                    @endif
                                  </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="6">
                                    Nenhuma fatura gerada.
                                </th>
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
