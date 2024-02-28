@extends('shop.layout.default')

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px;">
    <!-- Mask -->
    <span class="mask bg-gradient-default"></span>
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1 class="display-2 text-white">Comprar Radar</h1>
                <div class="button-container">
                    <a href="{{ route('shop.radar.index') }}" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">

    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <form method="POST" action="{{ route('shop.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card bg-secondary shadow">

                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="img_profile">CNH ou RG do responsável Legal da Empresa</label>
                                    <input type="file" class="form-control-file" id="img_profile" name="img_profile" accept="image/*" onchange="validateImage()">
                                </div>
                                <div class="form-group">
                                    <label for="img_profile">Contrato Social</label>
                                    <input type="file" class="form-control-file" id="img_profile" name="img_profile" accept="image/*" onchange="validateImage()">
                                </div>
                                <div class="form-group">
                                    <label for="img_profile">Extratos Bancários</label>
                                    <input type="file" class="form-control-file" id="img_profile" name="img_profile" accept="image/*" onchange="validateImage()">
                                    <small class="form-text text-muted">Dos últimos 3 meses.</small>

                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="img_profile">Cartão CNPJ</label>
                                    <input type="file" class="form-control-file" id="img_profile" name="img_profile" accept="image/*" onchange="validateImage()">
                                </div>
                                <div class="form-group">
                                    <label for="img_profile">Comprovante de Endereço</label>
                                    <input type="file" class="form-control-file" id="img_profile" name="img_profile" accept="image/*" onchange="validateImage()">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Whatsapp</label>
                                    <input type="text" class="form-control phone" name="phone" placeholder="(11) 11111-1111" value="{{ $authenticated_user->phone }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Agendar Horário</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">



                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right mt-2">
                    <button class="btn btn-lg btn-primary">Comprar AGORA</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $("#document_type").on('change', function(){
            change_doc_type();
        });

        function change_doc_type(){
            if($("#document_type").val() == 1){
                $("#document_label").html('CPF');
                $("#document").mask('000.000.000-00');
                $('.company_fields').hide();
            }else{
                $("#document_label").html('CNPJ');
                $("#document").mask('00.000.000/0000-00');
                $('.company_fields').show();
            }
        }

        change_doc_type();

        $("#document").on('focusout', function(){
            let document = $(this).val().replace(/[^\d]+/g,'');

            if(($("#document_type").val() == 1 && validarCPF(document)) || ($("#document_type").val() == 2 && validarCNPJ(document))){
                $(this).parent().find('.field_error').hide();
            }else{
                $(this).parent().find('.field_error').show();
            }
        });

    </script>
@endsection
