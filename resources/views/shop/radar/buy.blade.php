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
            <form method="POST" action="{{ route('shop.radar.update_buy') }}" enctype="multipart/form-data">
                @csrf
                <div class="card bg-secondary shadow">

                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="document">CNH ou RG do responsável Legal da Empresa</label>
                                    <input type="file" class="form-control-file" id="document" name="document" accept="application/pdf,image/png,image/jpeg" required>
                                    <small class="form-text text-muted">Foto no formato PNG, JPG, JPEG ou PDF</small>
                                </div>
                                <div class="form-group">
                                    <label for="social_contract">Contrato Social</label>
                                    <input type="file" class="form-control-file" id="social_contract" name="social_contract"  accept="application/pdf" required>
                                </div>
                                <div class="form-group">
                                    <label for="bank_extract">Extratos Bancários</label>
                                    <input type="file" class="form-control-file" id="bank_extract" name="bank_extract"  accept="application/pdf" required>
                                    <small class="form-text text-muted">Dos últimos 3 meses.</small>

                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="cnpj">Cartão CNPJ</label>
                                    <input type="file" class="form-control-file" id="cnpj" name="cnpj"  accept="application/pdf" required>
                                </div>
                                <div class="form-group">
                                    <label for="address_contract">Comprovante de Endereço</label>
                                    <input type="file" class="form-control-file" id="address_contract" name="address_contract"  accept="application/pdf" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-secondary shadow mt-4">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Observação!</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                O radar tem um valor de R$147,90. O pagamento é feito apenas uma única vez.
                                Após o pagamento ser realizado, em até 24h, nossa equipe entrara em contato para agendar sua inscricão.
                                Não se preocupe nossa equipe cuidara de tudo, você só precisa fornecer os documentos acima.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right mt-2">
                    <button class="btn btn-lg btn-primary">Comprar Agora</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
