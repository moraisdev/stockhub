@extends('shop.layout.default')

@section('title', config('app.name').' - Pagamentos do Plano')

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
            <div class="col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-xl-4">
                                        <h2 class="mb-0 d-inline-block">Detalhes da fatura</h2>
                                    </div>
                                    <div class="col-xl-8">
                                        <input type="hidden" id="id" name="id" value="{{$shop_invoice->id}}"/>
                                        @if (($shop_invoice->payment == 'pending') and  ($admins->pg_boleto == 0)) 
                                        <a href="{{ route('shop.plans.planspay', [ 'id' => $shop_invoice->id, 'payment_method' => 'boleto']) }}" class="btn btn-success float-right" hspace="5">Gerar boleto</a>
                                         
                                        @endif
                                         
                                         @if (($shop_invoice->payment == 'pending') and  ($admins->pg_pix == 0))
                                        <a href="{{ route('shop.plans.planspay', [ 'id' => $shop_invoice->id, 'payment_method' => 'pix']) }}" class="btn btn-default float-right">Pagar com Pix</a>
                                         @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                        
                        @if($shop_invoice->transaction_id_pix && $shop_invoice->qrcode_pix)
                            <div class='col-lg-3 mb-3'>                                
                                <img src="{{$shop_invoice->qrcode_pix}}" alt="qrcode do pix" style='max-width: 593px; width: 100%;'>
                            </div>
                        @endif

                        <div class="col-lg-9">
                        {{-- @if($shop_invoice->transaction_id_pix && $shop_invoice->qrcode_pix)  @endif --}}
                              
                                    <p style='margin-bottom: 0;'>Valor Plano: <b>R$ {{ number_format($shop_invoice->total, 2, ',','.') }}</b></p>
                                    <p class='mt-3'>Status do pagamento: <span class="badge badge-warning">{{ $shop_invoice->payment }}</span></p>
                                  
                                   
                                @if($shop_invoice->transaction_id_pix && $shop_invoice->key_pix)
                                {{-- <div class='col-lg-6'> --}}
                                    <h4 class='mt-5'>Código do Pix <button type="button" id='copy-pix-key' class="btn btn-primary btn-sm ml-3"><i class='ni ni-single-copy-04'></i> Copiar Código</button></h4>
                                    <textarea id='key-pix' class='form-control' readonly style='font-size: 10pt; font-weight: bold; padding: 5px; border: 1px solid #cecece;'>{{$shop_invoice->key_pix}}</textarea>                                    
                                    <small>{{$shop_invoice->description_pix}}</small><br />
                                    <small><span style="color: #fa3d3d;">*</span> Caso passe muito tempo e o QRCode esteja indisponível, clique em "Pagar com Pix" novamente para gerar outro.</small><br>
                                {{-- </div> --}}
                                @endif
                            </div>
                        </div>    

                @if($shop_invoice->transaction_id_pix)
               
                <div class="card shadow mt-4">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="mb-0">Você gerou um pix para esta fatura</h2>
                                <small>Confira os dados do pix gerado abaixo</small>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush align-items-center">
                            <thead>
                                <tr>
                                    <th>Plano</th>
                                    <th>Valor do Plano</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $shop_invoice->plan }}</td>
                                    <td>+ R$ {{ number_format($shop_invoice->total, 2, ',','.') }}</td>
                                </tr>
                                                                  
                            </tbody>
                        </table>                            
                    </div>
                </div>
           
             @endif

             @if($shop_invoice->transaction && $shop_invoice->bankslip_url)
                    <div class="card shadow mt-4">
                        <div class="card-header bg-transparent">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h2 class="mb-0">Você gerou um boleto para esta fatura</h2>
                                    <small>Confira os dados do boleto gerado abaixo</small>
                                </div>
                            </div>
                        </div>
                       
                        <div class="table-responsive">
                            <table class="table table-flush align-items-center">
                                <thead>
                                    <tr>
                                    <th>Plano</th>
                                    <th>Valor do Plano</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $shop_invoice->plan }}</td>
                                        <td>+ R$ {{ number_format($shop_invoice->total, 2, ',','.') }}</td>
                                    </tr>
                                                                      
                                </tbody>
                            </table>                            
                        </div>
                        <hr>
                        <div class="row d-flex justify-content-center pb-4">
                            {{-- <div class="col-md-12 text-center">
                                <h4>Data de vencimento <b>{{ $shop_invoice->bankslip_duedate  && $shop_invoice->bankslip_duedate  != '1970-01-01' ? date('d/m/Y', strtotime($shop_invoice->bankslip_duedate )) : 'Indisponível' }}</b></h4>
                            </div>                             --}}
                            <div class="col-md-12 text-center">
                                <a href="{{ $shop_invoice->bankslip_url }}" target="_blank" class="btn btn-info">Imprimir Boleto</a>
                            </div>
                        </div>
                        
                        
                    </div>
                @endif

                        
                        



                        </div>
                    </div>          
                    
            </div>
        </div>
    </div>

      
@endsection

@section('scripts')
    <script>
        function updateDeleteModalOrderInGroup(name, route){
            $('#name-order-delete').html(name)
            $("#form-delete-order-in-group").attr('action', route);
        }
    </script>

<script>

function atualizar()
{
    //var data = $(this).serialize();
    //var id = document.getElementById('id').innerText;
    var _token = $('meta[name="_token"]').attr('content');
    var id = document.getElementById('id').value;

    $.ajaxSetup({

    headers: {

    'X-CSRF-TOKEN': _token
    }
    });

   
    $.ajax({
    url:"{{ route('shop.consultapixplano')}}",
    type:"get",
    datatType : 'json',
    data: { id: id } ,
    success:function(data){
          console.log(data);
    //alert(data);
    },     
    error: function( data )
            {
                if(!data.responseJSON){
                    console.log(data.responseText);
                    $('#err').html(data.responseText);
                }else{
                    $('#err').html('');
                    $.each(data.responseJSON.errors, function (key, value) {
                        $('#err').append(key+": "+value+"<br>");
                        console.log(key);
                    });
                }
            }
        });
    
} 




setInterval("atualizar()", 10000);
</script>



@endsection