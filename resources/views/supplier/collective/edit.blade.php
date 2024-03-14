@extends('supplier.layout.default')

@section('stylesheets')
<style type="text/css">
    .thumbnail {
        width: 100%;
        height: 190px;
        background-size: cover;
        background-position: center;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 200px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <span class="mask bg-gradient-default opacity-8"></span>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Informações da Importação Coletiva') }}</h3>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('supplier.collective.update', $collective->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select class="form-control form-control-alternative" id="status" name="status">
                                            <option value="EM ANALISE" {{ $collective->status == 'EM ANALISE' ? 'selected' : '' }}>Em Análise</option>
                                            <option value="REJEITADO" {{ $collective->status == 'REJEITADO' ? 'selected' : '' }}>Rejeitado</option>
                                            <option value="CANCELADO" {{ $collective->status == 'CANCELADO' ? 'selected' : '' }}>Cancelado</option>
                                            <option value="PAGAMENTO PENDENTE" {{ $collective->status == 'PAGAMENTO PENDENTE' ? 'selected' : '' }}>Pagamento Pendente</option>
                                            <option value="PAGO" {{ $collective->status == 'PAGO' ? 'selected' : '' }}>Pago</option>
                                            <option value="ENVIADO" {{ $collective->status == 'ENVIADO' ? 'selected' : '' }}>Enviado</option>
                                            <option value="ENTREGUE" {{ $collective->status == 'ENTREGUE' ? 'selected' : '' }}>Entregue</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Cliente') }}</label>
                                        <input type="text" id="name" class="form-control form-control-alternative" name="name" placeholder="Nome do Cliente" value="{{ $collective->shop->corporate_name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="telefone">{{ __('Telefone') }}</label>
                                        <input type="text" id="phone" class="form-control form-control-alternative" name="phone" placeholder="Telefone" value="{{ $collective->shop->phone }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}</label>
                                        <input type="text" id="email" class="form-control form-control-alternative" name="email" placeholder="Email" value="{{ $collective->shop->email }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="documento">{{ __('Documento') }}</label>
                                        <input type="text" id="document" class="form-control form-control-alternative" name="document" placeholder="Documento" value="{{ $collective->shop->document }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="id">{{ __('ID do Cliente') }}</label>
                                        <input type="text" id="id" class="form-control form-control-alternative" name="id" placeholder="ID do Cliente" value="{{ $collective->shop->id }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="logradouro">{{ __('Logradouro') }}</label>
                                        <input type="text" id="street" class="form-control form-control-alternative" name="street" placeholder="Logradouro" value="{{ $collective->shop->address->street }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="numero">{{ __('Número') }}</label>
                                        <input type="text" id="numero" class="form-control form-control-alternative" name="numero" placeholder="Número" value="{{ $collective->shop->address->number }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="cep">{{ __('CEP') }}</label>
                                        <input type="text" id="zipcode" class="form-control form-control-alternative" name="zipcode" placeholder="CEP" value="{{ $collective->shop->address->zipcode }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="pais">{{ __('País') }}</label>
                                        <input type="text" id="country" class="form-control form-control-alternative" name="country" placeholder="País" value="{{ $collective->shop->address->country }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="bairro">{{ __('Bairro') }}</label>
                                        <input type="text" id="district" class="form-control form-control-alternative" name="district" placeholder="Bairro" value="{{ $collective->shop->address->district }}" readonly>

                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="estado">{{ __('Estado') }}</label>
                                        <input type="text" id="state_code" class="form-control form-control-alternative" name="state_code" placeholder="Estado" value="{{ $collective->shop->address->state_code }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="cidade">{{ __('Cidade') }}</label>
                                        <input type="text" id="city" class="form-control form-control-alternative" name="city" placeholder="Cidade" value="{{ $collective->shop->address->city }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="complemento">{{ __('Complemento') }}</label>
                                        <input type="text" id="complement" class="form-control form-control-alternative" name="complement" placeholder="Complemento" value="{{ $collective->shop->address->complement }}" readonly>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="complemento">{{ __('Data de Criação') }}</label>
                                        <input type="text" id="created_at" class="form-control form-control-alternative" name="created_at" placeholder="Data de Criação" value="{{ $collective->created_at }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="complemento">{{ __('Data da Ultima Atualização') }}</label>
                                        <input type="text" id="updated_at" class="form-control form-control-alternative" name="updated_at" placeholder="Data da Ultima Atualização" value="{{ $collective->updated_at }}" readonly>
                                    </div>
                                </div>
                                    <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="complemento">{{ __('Tipo') }}</label>
                                                <input type="text" id="type_order" class="form-control form-control-alternative" name="type_order" placeholder="Tipo" value="Pessoa Jurídica" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">Link do Produto</label>
                                            <input type="text" id="produto_link" class="form-control form-control-alternative" name="produto_link" placeholder="Link do Produto" value="{{ $collective->produto_link }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_description">Descrição e Informações</label>
                                            <input type="text" id="rejection_reason" class="form-control form-control-alternative" name="rejection_reason" placeholder="Descrição e Informações" value="{{ $collective->rejection_reason }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">Prazo de Entrega</label>
                                            <input type="text" id="delivery_deadline" class="form-control form-control-alternative" name="delivery_deadline" placeholder="Prazo de Entrega" value="{{ $collective->delivery_deadline }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">Preço de Custo</label>
                                            <input type="text" id="cost_price" class="form-control form-control-alternative" name="cost_price" placeholder="Preço de Custo" value="{{ $collective->cost_price }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="product_ncm">Código de Rastreio</label>
                                            <input type="text" id="tracking_code" class="form-control form-control-alternative" name="tracking_code" placeholder="Código de Rastreio" value="{{ $collective->tracking_code }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">Documentos</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-start">
                                    <a href="{{ route('supplier.download.invoice', $collective->id) }}" class="btn btn-primary mr-2">Baixar Invoice</a>
                                    <a href="{{ route('supplier.download.packingList', $collective->id) }}" class="btn btn-info">Baixar Packing List</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer pb-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="float-right form-group">
                                    <a href="{{ route('supplier.collective.index') }}" class="btn btn-secondary">{{ __('supplier.cancel') }}</a>
                                    <a href="{{ route('supplier.download.pdfImportCollective', $collective->id) }}" class="btn btn-secondary">{{ __('Gerar PDF da Importação') }}</a>
                                    <button class="btn btn-primary">{{ __('supplier.save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="d-none">
    <input type="hidden" id="new_variants_count" value="0">
    <input type="hidden" id="new_options_count" value="0">
    <input type="hidden" id="new_discounts_count" value="0">

    <div class='row new_discount' id='discount_example'>
        <div class='col-lg-6'>
            <div class='table-responsive'>
                <table class='table table-borderless variant-fields-table'>
                    <thead>
                        <tr>
                            <th>Quantidade</th>
                            <th>Desconto(%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class='form-group'>
                                    <input type="number" class='form-control form-control-alternative' name='new_discounts[0][quantity]' placeholder="Quantidade">
                                </div>
                            </td>
                            <td>
                                <div class='form-group'>
                                    <input type="number" step="0.01" class='form-control form-control-alternative' name='new_discounts[0][value]' placeholder="Desconto">
                                </div>
                            </td>
                            <td>
                                <button class='btn btn-danger btn-sm' type='button' onclick="remove_discount(this)">{{__('supplier.remove_discount')}}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group d-inline-block mr-1" style="max-width: 150px" id="example_option">
        <div class="input-group input-group-alternative flex-nowrap mb-3">
            <input type="text" class="form-control form-control-alternative" placeholder="{{ __('supplier.option_name') }}" name="options[]" value="" required>
            <div class="input-group-append">
                <button class="btn btn-sm btn-danger" type="button" onclick="remove_option(this)"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
<script type="text/javascript">
    document.getElementById('downloadInvoice').addEventListener('click', function() {
        var invoicePath = this.getAttribute('data-invoice-path');
        if (invoicePath) {
            window.open(invoicePath, '_blank');
        }
    });

    document.getElementById('downloadPackingList').addEventListener('click', function() {
        var packingListPath = this.getAttribute('data-packing-list-path');
        if (packingListPath) {
            window.open(packingListPath, '_blank');
        }
    });
</script>
@endsection
