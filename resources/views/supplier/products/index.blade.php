@extends('supplier.layout.default')

@section('title', __('supplier.product_tittle'))

@section('stylesheets')

<style type="text/css">
        .btn-circle {
            padding: 5px 8px;
            border-radius: 50%;
            font-size: 0.8rem;
            width: 2.0rem !important;
            height: 2.0rem !important;

        }

        .current {
background-color: #E4001B !important;
}



    </style>
@endsection

@section('content')
    <!-- Header -->
       

    <div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <!-- Card stats -->
                <div class="row">
                    {{-- <!--<div class="col-xl-4 col-12">
                                                                                                                                                                                                                                                                                                                 <a href="{{ route('supplier.products.show', $most_sold_variant->product_id) }}">{{ substr($most_sold_variant->title, 0, 40) }}{{ strlen($most_sold_variant->title) > 40 ? '...' : '' }}</a>
                                        @else
                                                                                                                                                                                                                                                                                                   {{ $most_sold_variant->sales_count }}
                                        @else
                                                                                                                                                                                                                                                                                          </div>--> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt--7" >
        <div class="row">
            <div class="col-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ __('supplier.products_tittle') }}</h3>
                            </div>
                            <div class="col-10">
                                <div class="float-right">
                                     <a class="btn btn-success" href="{{ route('supplier.products.import.csv_instructions') }}">
                                        <i class="fas fa-file-csv mr-2"></i> Importar Excel</a> 
                                    @if ($authenticated_user->bling_apikey)
                                        <button class="btn btn-success"  onclick="importAllProductsBling()"><i
                                                class="fas fa-arrow-down mr-2"></i>
                                            {{ __('supplier.import_products_from_bling') }} </button>
                                    @endif
                                    <a class="btn btn-primary" href="{{ route('supplier.products.create') }}"><i
                                            class="fas fa-plus mr-2"></i> {{ __('supplier.new_product') }} </a>
                                    <a class="btn btn-warning" href="#" id="massiveEdit"
                                        onclick="document.getElementById('formEdit').submit()"><i
                                            class="fas fa-edit mr-2"></i>
                                        Editar selecionados </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body my-0">
                        <p class="my-0"> {{ __('supplier.list_all_products') }} <span
                                class="text-orange">{{ __('supplier.product_hash') }}</span>
                            {{ __('supplier.to_shopkeeper') }}. </p>
                        {{-- <p class="my-0">List of all your products. You can give access to private products by providing the <span class="text-orange">product hash</span> to shop owners.</p> --}}

                    </div>

                  
                    <div class="col-12 table-responsive">
                            <table class="table align-items-center table-flush data-table mdl-data-table dataTable">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Imagem</th>
                                    <th scope="col">SKU</th>
                                    <th scope="col">Titulo</th>
                                    <th scope="col" class="text-center">Visibilidade</th>
                                    <th scope="col" class="actions-th">Ações</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                    </div>
                    <div class="card-footer py-4">
                        <div class="float-right">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="delete_modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="" id="delete_form">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('supplier.product_delete') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{ __('supplier.confirm_product_delete') }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('supplier.cancel') }}</button>
                            <button class="btn btn-danger">{{ __('supplier.delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="import_bling_modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Importando produtos do Bling</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    <img src="{{ asset('assets/img/Spinner-1s-200px (1).gif') }}" style="height: 30px;" id="imgok" ><a id ="txtstatus">Importando e Atualizando Produto Bling</a>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection



@section('scripts')



<script type="text/javascript">


 function importAllProductsBling() {
    

    var _token = $('meta[name="_token"]').attr('content');

    $.ajax({url:'{{ route('supplier.products.import.bling.json') }}',
        cache: false,
        type:'get',

        data: {   _token: _token   },
        beforeSend: function() {
            $("#import_bling_modal").modal('show');


        },
        success:function(response){
          res = response;
          console.log(res);
          if (res == "Importação OK") {
            document.querySelector("#txtstatus").innerHTML = "Importação Concluida" ;
            document.querySelector("#imgok").src = "{{asset('assets/img/confirm.gif')}}";
            console.log(res);




          }},
          error: function(response) {
            document.querySelector("#txtstatus").innerHTML = "Erro na Importação tente mais Tarde" ;
            document.querySelector("#imgok").src = "{{asset('assets/img/erro.gif')}}";



        }

});


}






    function update_delete_form_action(action) {
        $("#delete_form").attr('action', action);
    }
    $(document).ready(function(){
        $('#import_bling_modal').on('hidden.bs.modal', function () {
            location.reload();
        });
    });



</script>

<script>
 $(document).ready(function() { 

$('.data-table').DataTable({ 
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier.products.tabelas') }}",
        columns: [
            {data: 'id'},
            {data: 'img_source' , name : 'img_source'},           
            {data: 'sku'},
            {data: 'title'},
         
            {data: 'public'},

            {data: 'action', name: 'action', orderable: false, searchable: false},
            
        ],
        "language": {
                "lengthMenu": "Mostrando _MENU_ registros por página",
                "zeroRecords": "Nada encontrado",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro disponível",
                "infoFiltered": "(filtrado de _MAX_ registros no total)",
                 search: "Buscar Instituição:",
                 paginate: {
                 previous:   "<",
                 next:       ">",

        },

            }
     
    });
  });

  function show(id)
{
    let url = "{{ route('supplier.products.show', ':id') }}";
    url = url.replace(':id', id);
    document.location.href=url;
   // location.replace("{{ route('supplier.products.show',".id." )}}") ;
}

function edit(id)
{
    let url = "{{ route('supplier.products.edit', ':id') }}";
    url = url.replace(':id', id);
    document.location.href=url;
   
}

function excluir(id) { 

    $('#delete_modal').modal("show");
  
} 


function blingpost(id)
{
    let url = "{{ route('supplier.products.import.bling.json_a', ':id') }}";
    url = url.replace(':id', id);
    document.location.href=url;
   
}

  


</script>




@endsection
