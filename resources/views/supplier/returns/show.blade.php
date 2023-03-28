@extends('supplier.layout.default')

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

        
        
        .zoom-within-container {
         height: 300px; 
        overflow: hidden; 
        }
        .zoom-within-container img {
         transition: transform .5s ease;
        }
        .zoom-within-container:hover img {
        transform: scale(1.5);
        }

       
        .zoom-without-container {
        transition: transform .2s; 
        margin: 0 auto;
        }
        .zoom-without-container img{
	    width:100%;
	    height:auto;	
        }
        .zoom-without-container:hover {
         transform: scale(4.5); /* (150% zoom) */
        }

        .edit_hover_class{
        width:100px;
        height:100px;
        position: relative;
        }

        .edit_hover_class a{
        display:none;
        }
        .edit_hover_class:hover a {
        display:block;
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0; bottom: 0;
        left: 0; right: 0;
        margin: auto;
        z-index: 999;

        display: flex;
        justify-content: center;
        align-items: center;
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
                                <h2 class="mb-0">Solicitação de estorno do pedido {{ $return->supplier_order->f_display_id }} do lojista {{ $return->supplier_order->order->shop->name }}</h2>
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
                                                <div class="message {{ $message->supplier_id != null ? 'shop_message' : 'supplier_message'}}">
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
                                <form action="{{ route('supplier.returns.new_message', $return->id) }}" method="POST">
                                    @csrf
                                    <div class="chat-write d-flex flex-row">
                                        <input name="message" class="form-control flex-grow-1" id="message" placeholder="Digite sua mensagem" {{ $return->status == 'canceled' || $return->status == 'resolved' ? 'readonly' : '' }} minlength="1" required/>
                                        <button class="btn-primary btn flex-grow-0 ml-2" id="send-message-button" {{ $return->status == 'canceled' || $return->status == 'resolved' ? 'disabled' : '' }}>Enviar</button>
                                    </div>
                                </form>
                            </div>

                            <div class="row">
                                            
                                            <div class="col abrirModal edit_hover_class" style="margin-top:15px;" >
                                                @if($return->img_produto != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto) }}" alt="tag" width="60px"  height="60px" >    
                                                 <a href="#"><i class="fas fa-search-plus"></i></a>
                                                @endif
                                                
                                            </div>   
                                            <div class="col abrirModal edit_hover_class" style="margin-top:15px;" >
                                                @if($return->img_produto1 != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto1) }}" alt="tag" width="60px"  height="60px" >    
                                                 <a href="#"><i class="fas fa-search-plus"></i></a>
                                                @endif
                                                
                                            </div>   
                                            <div class="col abrirModal edit_hover_class" style="margin-top:15px;" >
                                                @if($return->img_produto2 != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto2) }}" alt="tag" width="60px"  height="60px" >    
                                                 <a href="#"><i class="fas fa-search-plus"></i></a>
                                                @endif
                                                
                                            </div>   
                                            <div class="col abrirModal edit_hover_class" style="margin-top:15px;" >
                                                @if($return->img_produto3 != '')
                                                
                                                <br>
                                                 <img class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto3) }}" alt="tag" width="60px"  height="60px" >    
                                                 <a href="#"><i class="fas fa-search-plus"></i></a>
                                                @endif
                                                
                                            </div>   
                                            <div class="col abrirModal edit_hover_class" style="margin-top:15px;" >
                                                @if($return->img_produto4 != '')
                                                
                                                <br>
                                                 <img  class="img_border" src="{{ asset('assets/imgdevprod/' . $return->img_produto4) }}" alt="tag" width="60px"  height="60px" >    
                                                 <a href="#"><i class="fas fa-search-plus"></i></a>
                                                @endif
                                                
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
                                                @if($return->supplier_return_confirmed == 'no')
                                                    <a href="{{ route('supplier.returns.confirm', [$return->id]) }}" class="btn btn-success">Marcar como resolvido</a>
                                                @else
                                                    <p class="mb-0">Você já confirmou o envio do reembolso para o lojista.</p>
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
    
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body text-center">
        <img src="" width="360px"  height="360px"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Sair</button>
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


        $(".abrirModal").click(function() {
         var url = $(this).find("img").attr("src");
         $("#myModal img").attr("src", url);
         $("#myModal").modal("show");
});
    </script>
@endsection
