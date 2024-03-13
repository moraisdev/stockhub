@extends('shop.layout.default')
<style>
.status-dot {
    height: 10px;
    width: 10px;
    background-color: #ee3a1f;
    border-radius: 50%;
    display: inline-block;
    margin-left:5px;
}

.status-text {
    color: #ee3a1f;
}

</style>

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 200px;">
    <!-- Mask -->
    <span class="mask bg-gradient-default"></span>

</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
        <form method="POST" action="{{ route('shop.collective.store') }}" enctype="multipart/form-data">
            @csrf
                <div class="card bg-secondary shadow">
                    <div class="card-body">
                        <div class="row">
                        <div class="col">
                            <label class="control-label">Tipo de Compra</label>
                            <select class="form-control" id="type_order" name="type_order">
                                <option value="1" {{ strlen($authenticated_user->type_order) == 11 ? 'selected' : '' }}>Pessoa Física</option>
                                <option value="2" {{ strlen($authenticated_user->type_order) == 14 ? 'selected' : '' }}>Pessoa Jurídica</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="invoice_path">Invoice</label>
                            <input type="file" class="form-control-file" id="invoice_path" name="invoice_path" accept="application/pdf">
                            <small class="form-text text-muted">Documento PDF</small>
                        </div>
                                 <div class="form-group">
                                    <label for="packing_list_path">Packing List</label>
                                    <input type="file" class="form-control-file" id="packing_list_path" name="packing_list_path"  accept="application/pdf">
                                    <small class="form-text text-muted">Documento PDF</small>
                                </div>
                            </div>
                                <div class="form-group">
                                    <label class="control-label">Link do Produto</label>
                                    <input type="text" class="form-control" name="produto_link" placeholder="https://www.alibaba.com/product-detail"required>
                                </div>
                                </div>
                        </div>
                <div class="form-group text-right mt-2">
                    <button class="btn btn-lg btn-primary">Enviar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
