@extends('shop.layout.default')

@section('title', 'Pré-visualizar')

@section('stylesheets')
<style type="text/css">

    .details {
        display: flex;
        max-width: 95%;
        gap: 18px;
    }
    .margin-bottom-custom {
    margin-bottom: 1rem; /* Ajuste o valor conforme necessário */
}
    .bg-light {
        background-color: #ffffff; 
    }
    .bg-custom {
        background-color: #f8f9fe;
    }

    .terms {
        font-size: 11px;
        margin: 15px 0;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .terms a {
        color: #000;
    }

    .extra {
        font-size: 10px;
    }

    .submit-btn {
        margin: 15px 0;
        border: none;
        background-color: #242542;
        color: #fff;
        height: 30px;
        font-size: 10px;
        border-radius: 3px;
        width: 45%;
    }

    .close {
        position: absolute;
        top: 5px;
        right: 5px;
        border: none;
        background-color: #eee;
        height: 30px;
        width: 30px;
        color: gray;
        font-size: 16px;
        text-align: center;
        border-radius: 50%;
        cursor: pointer;
    }

    .thumbnail {
        width: 100%;
        height: 250px;
        background-size: cover;
        background-position: center;
        margin-bottom: 20px;
    }

    .general-information img {
        max-width: 100%;
        height: auto;
    }

    .h5 {
        font-size: 2em;
        font-weight: bold;
        color: #d9534f;
        margin-bottom: 1rem;
    }

    .icon-hover:hover {
        border-color: #3b71ca !important;
        background-color: white !important;
        color: #3b71ca !important;
    }

    .icon-hover:hover i {
        color: #3b71ca !important;
    }
    .general-information {
        margin-top: 20px;
        margin: 15px;
    }
</style>
@endsection

@section('content')
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-default"></span>
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-12">
                <a href="{{ url('/shop/catalog') }}" class="btn btn-light mb-3">Voltar</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-2">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row gx-5">
                        <aside class="col-lg-6">
                            <div class="border rounded-4 mb-3 d-flex justify-content-center">
                                <img id="main-image" style="max-width: 100%; max-height: 100vh; margin: auto;"
                                    class="rounded-4 fit"
                                    src="{{ 'data:image/jpeg;base64,' . $product->img_source_data }}" />
                            </div>
                            <div class="d-flex justify-content-center mb-3">
                                @forelse($product->images as $index => $image)
                                <div class="border mx-1 rounded-2 thumbnail-img" id="thumbnail-{{ $index }}">
                                    <img width="60" height="60" class="rounded-2"
                                        src="{{ 'data:image/jpeg;base64,' . $image->image_data }}" />
                                </div>
                                @empty
                                <p>Nenhuma imagem adicional cadastrada.</p>
                                @endforelse
                            </div>
                        </aside>
                        <main class="col-lg-6">
                            <div class="ps-lg-3">
                                <h4 class="title text-dark">
                                    {{ $product->title }}
                                </h4>
                                <div class="mb-3">
                                    <span class="h5">${{ $product->variants->first()->price }}</span>
                                </div>

                                <p>
                                    {!! nl2br(e($product->description)) !!}
                                </p>
                                <hr />

         <!-- Início das opções de variantes -->
         <div class="row mb-3">
            @php
            $variantOptions = $product->variants->flatMap(function ($variant) {
                return $variant->options_values;
            })->groupBy('product_option_id');
            @endphp

            @foreach ($variantOptions as $optionId => $values)
            @php
            $option = \App\Models\ProductOptions::find($optionId);
            @endphp

            @if ($option)
            <div class="col-md-4 col-6">
                <label class="mb-2">{{ $option->name }}</label>
                <select class="form-select border border-secondary" style="height: 35px;">
                    @foreach ($values as $value)
                    <option value="{{ $value->id }}">{{ $value->value }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            @endforeach
        </div>
        <!-- Fim das opções de variantes -->

        <!-- Início da quantidade -->
        <div class="row">
        <div class="col-md-4 col-6 mb-3 margin-bottom-custom"> <!-- Adicionada a classe aqui -->
                <label class="mb-2 d-block">Quantidade</label>
                <div class="input-group" style="width: 170px;">
                    <button class="btn btn-white border border-secondary px-3" type="button"
                            id="button-addon1" data-mdb-ripple-color="dark">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="text" class="form-control text-center border border-secondary"
                           id="quantity-input" placeholder="1" aria-label="Quantity" 
                           aria-describedby="button-addon1" value="1" />
                    <button class="btn btn-white border border-secondary px-3" type="button"
                            id="button-addon2" data-mdb-ripple-color="dark">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Fim da quantidade -->


                                <a href="#" class="btn btn-warning shadow-0" id="buy-now-btn"> Comprar agora </a>
                                <a href="#" class="btn btn-primary shadow-0 add-to-cart-btn" id="add-to-cart-btn"> <i class="me-1 fa fa-shopping-basket"></i> Adicionar ao carrinho </a>
                            </div>
                        </main>
                    </div>
                </div>
                </section>
                <!-- content -->
                <section class="bg-custom border-top py-4">
                    <div class="container">
                        <div class="row gx-4">
                            <div class="col-lg-8 mb-4">
                                <div class="border rounded-2 px-3 py-2 bg-white">
                                    <!-- Navegação das abas -->
                                    <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="ex1-tab-1" data-toggle="pill"
                                                href="#ex1-pills-1" role="tab" aria-controls="ex1-pills-1"
                                                aria-selected="true">Informações Gerais</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="ex1-content">
                                        <div class="tab-pane fade show active" id="ex1-pills-1" role="tabpanel"
                                            aria-labelledby="ex1-tab-1">
                                            <p>
                                                Garantia StockHub
                                            </p>
                                            <div class="row mb-2">
                                                <div class="col-12 col-md-6">
                                                    <ul class="list-unstyled mb-0">
                                                        <li><i class="fas fa-check text-success me-2"></i> Pagamentos
                                                            Seguros: Métodos de pagamentos utilizados por diversos
                                                            compradores nacionais e internacionais</li>
                                                    </ul>
                                                </div>
                                                <div class="col-12 col-md-6 mb-0">
                                                    <ul class="list-unstyled">
                                                        <li><i class="fas fa-check text-success me-2"></i> Segurança e
                                                            privacidade: Respeitamos sua privacidade portanto seus dados
                                                            permanecem seguros.</li>
                                                    </ul>
                                                </div>
                                                <div class="general-information">
                                                    {!! $product->general_information !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="px-0 border rounded-2 shadow-0">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Outros Produtos</h5>
                                            @foreach ($otherProducts as $otherProduct)
                                            @if(isset($otherProduct->hash))
                                            <div class="d-flex mb-3">
                                                <a href="{{ route('shop.products.details', $otherProduct->hash) }}"
                                                    class="me-3">
                                                    <img src="{{ 'data:image/jpeg;base64,' . $otherProduct->img_source_data }}"
                                                        style="min-width: 96px; height: 96px;"
                                                        class="img-md img-thumbnail" />
                                                </a>
                                                <div class="info">
                                                    <a href="{{ route('shop.products.details', $otherProduct->hash) }}"
                                                        class="nav-link mb-1">
                                                        {{ $otherProduct->title }}
                                                    </a>
                                                    <strong class="text-dark">${{
                                                        $otherProduct->variants->first()->price }}</strong>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </section>
            @endsection
            @section('scripts')
            @section('scripts')
<script>
    $(document).ready(function() {
        // Switching main image with thumbnail image on click
        document.querySelectorAll('.thumbnail-img img').forEach((img, index) => {
            img.addEventListener('click', function() {
                const mainImage = document.getElementById('main-image');
                const thumbnail = document.getElementById(`thumbnail-${index}`).getElementsByTagName('img')[0];
                let tempSrc = mainImage.src;
                mainImage.src = thumbnail.src;
                thumbnail.src = tempSrc;
            });
        });

        // Toggle tabs without URL change
        $('a[data-toggle="pill"]').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Update quantity function
        function updateQuantity(isIncreasing) {
            var quantityInput = document.getElementById('quantity-input');
            var currentQuantity = parseInt(quantityInput.value);
            if (isIncreasing) {
                currentQuantity++;
            } else if (currentQuantity > 1) {
                currentQuantity--;
            }
            quantityInput.value = currentQuantity;
        }

        // Event listeners for quantity update buttons
        document.getElementById('button-addon1').addEventListener('click', function() {
            updateQuantity(false);
        });

        document.getElementById('button-addon2').addEventListener('click', function() {
            updateQuantity(true);
        });

        // "Comprar Agora" button click event
        document.getElementById('buy-now-btn').addEventListener('click', function(event) {
            event.preventDefault();
            buyNow();
        });

        function buyNow() {
            var productData = {
                product_hash: '{{ $product->hash }}',
                quantity: document.getElementById('quantity-input').value,
            };

            fetch('{{ route('shop.orders.buy') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(productData)
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response                 
                console.log(data);
                // Redirect or show success message
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>
@endsection
