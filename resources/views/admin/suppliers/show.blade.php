@extends('admin.layout.default')

@section('title', 'Detalhes do fornecedor')

@section('content')
<div class="header pb-6 pt-4 pt-lg-6 d-flex align-items-center" style="min-height: 400px; background-image: url(https://wallpapertag.com/wallpaper/full/5/9/b/664802-vertical-flat-design-wallpapers-1920x1080.jpg); background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1 class="display-2 text-white">{{ $supplier->name }}</h1>
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <form action="{{ route('admin.suppliers.salve', [$supplier->id]) }}" method="POST">
                @csrf
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <h3 class="mb-0 d-inline-block">Detalhes do Fornecedor</h3>
                                <div class="d-inline-block float-right">
                                @if ($admins->plano_f == 2)
                                    @if($supplier->safe2pay_subaccount_id == null)
                                        <a href="{{ route('admin.suppliers.send_to_safe2pay', [$supplier->id]) }}" class="btn btn-info">Enviar p/ Safe2Pay</a>
                                    @else
                                        <span class="badge badge-info">Safe2Pay ID: {{ $supplier->safe2pay_subaccount_id }}</span>
                                    @endif
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">Nome no {{config('app.name')}}</label>
                                        <input type="text" class="form-control" name="name" value="{{ $supplier->name }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="{{ $supplier->email }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Seu nome completo</label>
                                        <input type="text" class="form-control" name="responsible_name" id="responsible_name" value="{{ $supplier->responsible_name }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Seu CPF</label>
                                        <input type="text" class="form-control" name="responsible_document" id="responsible_document" value="{{ $supplier->responsible_document }}" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">Nome Fantasia</label>
                                        <input type="text" class="form-control" name="commercial_name" value="{{ $supplier->commercial_name }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Razão Social</label>
                                        <input type="text" class="form-control" name="legal_name" value="{{ $supplier->legal_name }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">CNPJ</label>
                                        <input type="text" class="form-control" id="document" name="document" value="{{ $supplier->document }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Telefone</label>
                                        <input type="text" class="form-control phone" name="phone" value="{{ $supplier->phone }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Endereço</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">Endereço</label>
                                        <input type="text" class="form-control" name="street" placeholder="Endereço" value="{{ $supplier->address ? $supplier->address->street : '' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Número</label>
                                        <input type="text" class="form-control" name="number" placeholder="Número" value="{{ $supplier->address ? $supplier->address->number : '' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Bairro</label>
                                        <input type="text" class="form-control" name="address2" placeholder="Bairro" value="{{ $supplier->address ? $supplier->address->district : '' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Complemento</label>
                                        <input type="text" class="form-control" name="complement" value="{{ $supplier->address ? $supplier->address->complement : '' }}" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">CEP</label>
                                        <input type="text" class="form-control cep" name="zipcode" value="{{ $supplier->address ? $supplier->address->zipcode : '' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">País</label>
                                        <select class="form-control" name="country" id="country_select" readonly>
                                            <option value="Brasil">Brasil</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Estado</label>
                                        <input type="text" class="form-control" name="province" placeholder="Estado" value="{{ $supplier->address ? $supplier->address->state_code : '' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Cidade</label>
                                        <select class="form-control" name="city" id="city_select" readonly>
                                            <option value="">{{ $supplier->address ? $supplier->address->city : 'Não selecionada.' }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Safe2Pay e Conta Bancária</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">Método de recebimento/pagamento</label>
                                        <select name="payment_method" class="form-control" readonly>
                                            <option value="Safe2Pay">Safe2Pay</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Nome do responsável técnico</label>
                                        <input type="text" class="form-control" name="tech_name" id="tech_name" value="{{ $supplier->tech_name }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">CPF do responsável técnico</label>
                                        <input type="text" class="form-control" name="tech_document" id="tech_document" value="{{ $supplier->tech_document }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">E-mail do responsável técnico</label>
                                        <input type="text" class="form-control" name="tech_email" id="tech_email" value="{{ $supplier->tech_email }}" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">Banco</label>
                                        <select class="form-control" name="bank[code]" id="bank_list" readonly>

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Tipo de Conta</label>
                                        <select class="form-control" name="bank[account_type]" readonly>
                                            <option value="cc" {{ $supplier->bank && $supplier->bank->account_type == 'cc' ? 'selected' : '' }}>Conta Corrente</option>
                                            <option value="pp" {{ $supplier->bank && $supplier->bank->account_type == 'pp' ? 'selected' : '' }}>Conta Poupança</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Agência</label>
                                        <div class="row">
                                            <div class="col-lg-9 col-12">
                                                <input type="text" class="form-control" name="bank[agency]" value="{{ $supplier->bank ? $supplier->bank->agency : '' }}" readonly>
                                            </div>
                                            <div class="col-lg-3 col-12">
                                                <input type="text" class="form-control" name="bank[agency_digit]" value="{{ $supplier->bank ? $supplier->bank->agency_digit : '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Conta</label>
                                        <div class="row">
                                            <div class="col-lg-9 col-12">
                                                <input type="text" class="form-control" name="bank[account]" value="{{ $supplier->bank ? $supplier->bank->account : '' }}" readonly>
                                            </div>
                                            <div class="col-lg-3 col-12">
                                                <input type="text" class="form-control" name="bank[account_digit]" value="{{ $supplier->bank ? $supplier->bank->account_digit : '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Taxas</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="control-label">Taxa de processamento {{config('app.name')}} (%)</label>
                                        <input type="text" class="form-control" name="mawa_post_tax" placeholder="Exemplo: 3.5" value="{{ $supplier->mawa_post_tax }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary float-right">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        var bank_code = {!! $supplier->bank ? $supplier->bank->code : 0 !!}

        $.ajax({
            url: '/js/banklist.json',
            success: function(banks){
                $.each(banks, function(index, bank){
                    if(bank.value == bank_code){
                        $("#bank_list").append('<option value="'+bank.value+'" selected>'+bank.label+'</option>');
                    }else{
                        $("#bank_list").append('<option value="'+bank.value+'">'+bank.label+'</option>');
                    }
                });
            }
        })
    </script>
@endsection
