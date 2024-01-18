@extends('shop.layout.default')

@section('title', config('app.name').' - Detalhes da compra')

@section('stylesheets')
    <style>
        .chat-body{
            height: 500px;
            overflow-y: scroll;
            overflow-x: hidden;
        }

        .chat-write {
            margin-top: 20px;
            padding: 5px;
        }

        .message{
            border-radius: 10px;
            padding:20px;
            margin-bottom: 10px;
            max-width: 60%;
            margin:10px;
        }

        .shop_message{
            background-color: #DCF8C6;
            float: right;
        }

        .supplier_message{
            background-color: #F8F9FE;
        }

        .image-input {
            position: relative;
            display: inline-block;
            border-radius: 0.42rem;
            background-repeat: no-repeat;
            background-size: cover; }
        .image-input .image-input-wrapper {
            width: 120px;
            height: 120px;
            border-radius: 0.42rem;
            background-repeat: no-repeat;
            background-size: cover; 
        }
        .image-input [data-action="change"] {
            cursor: pointer;
            position: absolute;
            right: -10px;
            top: -10px; 
        }
        .image-input [data-action="change"] input {
            width: 0 !important;
            height: 0 !important;
            overflow: hidden;
            opacity: 0; 
        }
        .image-input [data-action="cancel"],
        .image-input [data-action="remove"] {
            position: absolute;
            right: -10px;
            bottom: -5px; 
        }
        .image-input [data-action="cancel"] {
            display: none;
         }
        .image-input.image-input-changed [data-action="cancel"] {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
         }
        .image-input.image-input-changed [data-action="remove"] {
            display: none;
         }
        .image-input.image-input-empty [data-action="remove"],
        .image-input.image-input-empty [data-action="cancel"] {
            display: none;
         }
        .image-input.image-input-circle {
            border-radius: 50%;
         }
            .image-input.image-input-circle .image-input-wrapper {
            border-radius: 50%;
         }
            .image-input.image-input-circle [data-action="change"] {
            right: 5px;
            top: 5px;
         }
            .image-input.image-input-circle [data-action="cancel"],
            .image-input.image-input-circle [data-action="remove"] {
            right: 5px;
            bottom: 5px;
         }
        .image-input.image-input-outline .image-input-wrapper {
            border: 3px solid #ffffff;
            -webkit-box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075); 
        }
        .btn.btn-icon i {
             padding-top: 0px;
             margin: -10px;

            }
        .btn.btn-bg-white {
        background-color: #ffffff;
        border-color: #ffffff;
     }
        .btn.btn-bg-white.disabled, .btn.btn-bg-white:disabled {
        background-color: #ffffff;
        border-color: #ffffff;
     }
     .btn.btn-icon.btn-circle {
        border-radius: 50%; 
     }
    .btn.btn-icon.btn-xs {
     height: 36px;
     width: 36px; 
     }

    .img_border {
      border-width: 2px;
      border-style: dashed;
      border-color: #FF0000;
    }
        

    </style>
@stop

@section('content')
    <!-- Header -->
    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
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
                                <h2 class="mb-0">Solicitação de estorno do pedido F{{ $return->supplier_order->display_id }} do fornecedor {{ $return->supplier_order->supplier->name }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <td>Data da solicitação</td>
                                        <td>{{ date('d/m/Y', strtotime($return->created_at)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{ $return->f_status }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-xl-6">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4>Chat do estorno</h4>
                                    </div>
                                </div>
                                <div class="chat-body" id="chat-body">
                                    @forelse($return->messages as $message)
                                        <div class="row">
                                            <div class="col">
                                                <div class="message {{ $message->shop_id != null ? 'shop_message' : 'supplier_message'}}">
                                                    {{ $message->message }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="row h-100">
                                            <div class="col d-flex flex-row align-items-center justify-content-center">
                                                <span class="badge badge-light align-items-center">Nenhuma mensagem ainda, comece explicando o motivo do estorno</span>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>


                                <form action="{{ route('shop.orders.return.new_essage') }}" method="POST">
                                    @csrf
                                    <div class="chat-write d-flex flex-row">
                                        <input name="message" class="form-control flex-grow-1" id="message" placeholder="Digite sua mensagem" {{ $return->status == 'canceled' || $return->status == 'resolved' ? 'readonly' : '' }} minlength="1" required/>
                                        <input type="hidden" name="supplier_order_id" value="{{ $return->supplier_order_id }}">
                                        
                                        <button class="btn-primary btn flex-grow-0 ml-2" id="send-message-button" {{ $return->status == 'canceled' || $return->status == 'resolved' ? 'disabled' : '' }}>Enviar</button>
                                        
                                        
                                        
                                   </form>
                                   <form enctype="multipart/form-data" action="{{ route('shop.orders.return.new_essage_img', $return->id ) }}" method="POST">
                                                @csrf

                                <button type="button" class="btn btn-white" aria-label="Left Align" data-toggle="modal" data-target="#exampleModal">
                                <i class="fa fa-file" aria-hidden="true"></i>
                                </button>
                                 <form>   
                                   
                                    </div>
                                
                                               

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h4>Opções</h4>
                                        <div class="row">
                                            <div class="col">
                                                @if($return->shop_return_confirmed == 'no')
                                                    <a href="{{ route('shop.returns.confirm', [$return->id]) }}" class="btn btn-success">Marcar como resolvido</a>
                                                @endif
                                                @if($return->status == 'pending')
                                                    <a href="{{ route('shop.returns.cancel', [$return->id]) }}" class="btn btn-danger">Cancelar solicitação de estorno</a>
                                                @endif
                                                @if($return->status == 'resolved')
                                                    <p class="mb-0">Você já confirmou o recebimento do reembolso.</p>
                                                @endif
                                                
                                                
                                                <div class="row">
                                                <div class="col" style="margin-top:15px;" >
                                                @if($return->img_produto != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto) }}" alt="tag" width="60px"  height="60px" >    
                                                
                                                @endif
                                                
                                            </div>   
                                            <div class="col" style="margin-top:15px;" >
                                                @if($return->img_produto1 != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto1) }}" alt="tag" width="60px"  height="60px" >    
                                                
                                                @endif
                                                
                                            </div>   
                                            <div class="col zoom-without-container" style="margin-top:15px;" >
                                                @if($return->img_produto2 != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto2) }}" alt="tag" width="60px"  height="60px" >    
                                                
                                                @endif
                                                
                                            </div>   
                                            <div class="col" style="margin-top:15px;" >
                                                @if($return->img_produto3 != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto3) }}" alt="tag" width="60px"  height="60px" >    
                                                
                                                @endif
                                                
                                            </div>   
                                            <div class="col" style="margin-top:15px;" >
                                                @if($return->img_produto4 != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto4) }}" alt="tag" width="60px"  height="60px" >    
                                                
                                                @endif
                                                
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
            </div>
        </div>
        <div class="modal" tabindex="-1" role="dialog" id="exampleModal" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Adcione as Imagens do Produto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form enctype="multipart/form-data" action="{{ route('shop.orders.return.new_essage_img', $return->id ) }}" method="POST">
       @csrf
                                       
       <div class="custom-file">
                                               
       <input type="file" class="form-control"  name="img_produto">       
       </div>
       <div class="custom-file">
                                               
       <input type="file" class="form-control"  name="img_produto1">       
       </div>
       <div class="custom-file">
                                               
       <input type="file" class="form-control"  name="img_produto2">       
       </div>
       <div class="custom-file">
                                               
       <input type="file" class="form-control"  name="img_produto3">       
       </div>
       <div class="custom-file">
                                               
       <input type="file" class="form-control"  name="img_produto4">       
       </div>

                       
      

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="submit" >Salvar</button>
        </form> 
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Sair</button>
      </div>
    </div>
  </div>
</div>


    </div>


@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            updateScroll()
        })

        function updateScroll(){
            var element = document.getElementById("chat-body");
            element.scrollTop = element.scrollHeight;
        }

        $('#send-message-button').click(function(){
            sendMessage()
        })

        

        function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        $(input).next()
        .attr('src', e.target.result)
    };
    reader.readAsDataURL(input.files[0]);
    }
    else {
        var img = input.value;
        $(input).next().attr('src',img);
    }
}


    </script>
@endsection