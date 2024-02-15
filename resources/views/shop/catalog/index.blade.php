@extends('shop.layout.default')

@section('title', 'Loja de Importação')

@section('stylesheets_before')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/js/slick/slick.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('assets/js/slick/slick-theme.css') }}" />

<style>
    .product-card {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .product-card-header {
        text-align: center;
        padding: 15px;
    }
    .china-flag {
        height: 22px;
        width: 22px;
        position: absolute;
        right: 0.5rem;
        top: 0.5rem;
        z-index: 2; 
    }

    .product-image {
        max-width: 100%;
        height: auto;
        border-bottom: 1px solid #f0f0f0;
    }

    .product-title {
        color: #333;
        font-size: 1.2em;
        margin-top: 10px;
    }

    .product-description {
        color: #666;
        font-size: 0.9em;
        margin-top: 5px;
    }

    .product-card-body {
        padding: 10px 15px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .button-container {
    width: 100%;
    }
    .button-container .btn {
        width: 100%;
        box-sizing: border-box;
    }
    .product-pricing {
        margin-bottom: 10px;
        text-align: center;
    }
    .product-pricing-left {
        width: 100%;
        text-align: center;
        margin-bottom: 10px;
    }

    .price {
        color: #000000;
        font-weight: bold;
        font-size: 1.2em;
    }

    .btn {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        color: #fff;
        text-align: center;
        margin-right: 5px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #007BFF;
    }

    .btn-secondary {
        background-color: #6C757D;
    }

    .col-xl-4, .col-lg-6, .col-12 {
        box-sizing: border-box;
        padding: 10px;
    }

    @media (max-width: 991px) {
        .col-lg-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 767px) {
        .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    @media (min-width: 768px) {
    .product-card-body {
        align-items: flex-start; 
    }

    .product-pricing-left {
        text-align: left; 
    }

    .button-container {
        justify-content: flex-start; 
    }
}

    .product-card-body .btn {
        padding: 8px 12px; 
        margin: 0; 
        flex-grow: 0; 
        flex-shrink: 0;
        white-space: nowrap;
    }

    .selected_category {
        background-color: #2e43ba !important;
    }

    .slide-reponsive-desktop {
        display: none !important;
    }

    .slide-reponsive-mobile {
        display: block !important;
    }

    @media(min-width: 1000px) {
        .slide-reponsive-desktop {
            display: block !important;
        }

        .slide-reponsive-mobile {
            display: none !important;
        }
    }

    .img-mobile {
        display: none;
    }

    @media(max-width: 767px) {
        .img-mobile {
            display: block;
        }

        .img-desktop {
            display: none;
        }
    }

    .countdown-style {
        font-size: 18px;
        color: #333;
        background: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        margin-top: 10px;
    }

    .countdown-container {
        text-align: center;
    }

    .countdown-title {
        margin-bottom: 10px;
    }
</style>

@endsection

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
<span class="mask bg-gradient-default"></span>
    <div class="container-fluid">
        <div class="header-body">
            <div id="carouselHome" class="carousel slide carousel-home-custom mb-4" data-ride="carousel"
                data-interval='5000'>
                <div class="carousel-inner">
                    @if(isset($banners[0]))
                    <div class="carousel-item active">
                        <a href="https://docs.google.com/forms/d/e/1FAIpQLSfS7APOzZ-5mmomnbPrBoFqFwYicWj--z1G2d3z7AMQn2CCdA/viewform"
                            target='_blank'>
                            <img class="d-block w-100 slide-reponsive-desktop" src="{{$banners[0]->img_source}}">
                            <img class="d-block w-100 slide-reponsive-mobile" src="{{$banners[0]->img_source_mobile}}">
                        </a>
                    </div>
                    @endif
                    @if(isset($banners[1]))
                    <div class="carousel-item">
                        <a href="#" target='_blank'>
                            <img class="d-block w-100 slide-reponsive-desktop"
                                src="{{asset('assets/static/banners/03banner_desktop_1953x228px.jpg')}}">
                            <img class="d-block w-100 slide-reponsive-mobile"
                                src="{{asset('assets/static/banners/03banner_mobile_800x800px.jpg')}}">
                        </a>
                    </div>
                    @endif
                    @if(isset($banners[2]))
                    <div class="carousel-item">
                        <a href="https://api.whatsapp.com/send?phone=5511987155948&text=Ol%C3%A1%2C%20conheci%20a%20PJ%20Rocks%20pela%20Mawa%20Post%20e%20gostaria%20de%20obter%20mais%20informa%C3%A7%C3%B5es%20por%20favor"
                            target='_blank'>
                            <img class="d-block w-100 slide-reponsive-desktop"
                                src="{{asset('assets/static/banners/banner_desktop_1953x228px.jpg')}}">
                            <img class="d-block w-100 slide-reponsive-mobile"
                                src="{{asset('assets/static/banners/banner_mobile_800x800px.jpg')}}">
                        </a>
                    </div>

                    @endif
                </div>
                <a class="carousel-control-prev" href="#carouselHome" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Voltar</span>
                </a>
                <a class="carousel-control-next" href="#carouselHome" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Próximo</span>
                </a>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total pendente</h5>
                                    <span class="h2 font-weight-bold mb-0">R$ {{
                                        number_format($dashboard_data['total_pending'],2,',','.') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total de pedidos</h5>
                                    <span class="h2 font-weight-bold mb-0">R$ {{
                                        number_format($dashboard_data['total_cost'],2,',','.') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12"></div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="countdown-container">
                                <div class="countdown-title">
                                    <h5 class="card-title text-uppercase text-muted mb-0">🚚 O lote fecha em:</h5>
                                </div>
                                <div id="countdown" class="countdown-style"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-4 col-12">
                            @if(isset($supplier))
                            <h2 class="mb-0">Catálogo: {{ $supplier->name }}</h2>
                            @else
                            <h2>Catálogo de Produtos</h2>
                            @endif
                        </div>
                        <div class="col-md-8 col-12">
                            <div class="form-group">
                                <input type="text" class="form-control float-right" name="search" id="search"
                                    placeholder="Pesquisar por nome" onkeyup="search()">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="slick mx-3 mt-3">
                                @foreach($categories as $category)
                                <button type="button" class="btn btn-primary btn-square category-button"
                                    category_id="{{ $category->id }}" onclick="selectCategory(this)">{{ $category->name
                                    }} </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class='row mt-3' hidden>
                        <div class="col-12">
                            <h2 class="mb-2">Origem dos produtos:</h2>
                        </div>
                    </div>
                    <div class='row' hidden>
                        <div class="col-1">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="products_from_brasil"
                                    name='products_from_brasil' checked>
                                <label class="custom-control-label" for="products_from_brasil">Brasil</label>
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="products_from_china"
                                    name='products_from_china' checked>
                                <label class="custom-control-label" for="products_from_china">China</label>
                            </div>
                        </div>
                    </div>
                </div>
                @if(isset($supplier))
                <div class="card-body">
                    <small>Listagem de produtos do fornecedor {{ $supplier->name }}.</small>
                </div>
                @endif

            </div>
        </div>
    </div>
    <div class="row" id="products-container">
        @if($products->count() == 0)
        <div class="col-12">
            <div class="card mt-4 shadow">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">Sentimos muito, não há nenhum produto disponível no momento.</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col mt-2">
            <div class="float-right">
                <p class="text-right">Página <span id="page"></span></p>
                <a href="javascript:prevPage()" class="btn btn-primary" id="btn_prev">Página Anterior</a>
                <a href="javascript:nextPage()" class="btn btn-primary" id="btn_next">Próxima Página</a>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script type="text/javascript">
    $(".slick").slick({
        speed: 300,
        slidesToShow: 6,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1600,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            }

        ]
    });

    var all_products = {!! $products !!};
    var products = all_products;
    var current_page = 1;
    var records_per_page = 12;

    products = shuffle(products);

    $('#products_from_brasil').on('click', function () {
        search();
    })

    $('#products_from_china').on('click', function () {
        search();
    })

    function selectCategory(obj) {
        current_page = 1
        $('.category-button').removeClass('selected_category');

        $(obj).addClass('selected_category');
        search();
    }

    function search() {
        let toSearch = $('#search').val();
        let categoryToSearch = $('.selected_category').attr('category_id');

        products = new Array();

        for (var i = 0; i < all_products.length; i++) {
            let city_state = all_products[i].supplier.sku ? all_products[i].supplier.id.toLowerCase() + '/' + all_products[i].supplier.id.toLowerCase() : '';

            console.log(city_state)
            if (categoryToSearch && categoryToSearch != 'all') {
                if (all_products[i].category_id == categoryToSearch && (all_products[i].title.toLowerCase().indexOf(toSearch.toLowerCase()) != -1 || city_state.indexOf(toSearch.toLowerCase()) != -1)) {
                    if ($('#products_from_brasil').is(':checked') && all_products[i].products_from === 'BR') {
                        products.push(all_products[i]);
                    }

                    if ($('#products_from_china').is(':checked') && all_products[i].products_from === 'CN') {
                        products.push(all_products[i]);
                    }
                }
            } else {
                if (all_products[i].title.toLowerCase().indexOf(toSearch.toLowerCase()) != -1 || city_state.indexOf(toSearch.toLowerCase()) != -1) {
                    if ($('#products_from_brasil').is(':checked') && all_products[i].products_from === 'BR') {
                        products.push(all_products[i]);
                    }

                    if ($('#products_from_china').is(':checked') && all_products[i].products_from === 'CN') {
                        products.push(all_products[i]);
                    }
                }
            }
        }

        changePage(1);
    }

    function prevPage() {
        if (current_page > 1) {
            current_page--;
            changePage(current_page);
        }
    }

    function nextPage() {
        if (current_page < numPages()) {
            current_page++;
            changePage(current_page);
        }
    }

    function changePage(page) {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var listing_table = document.getElementById("products-container");
        var page_span = document.getElementById("page");

        // Validate page
        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();

        listing_table.innerHTML = "";

        if (products.length > 0) {
            for (var i = (page - 1) * records_per_page; i < (page * records_per_page) && i < products.length; i++) {
                listing_table.innerHTML += printProduct(products[i]);
            }
        } else {
            listing_table.innerHTML += '<div class="col-12"><div class="card mt-4 shadow"><div class="card-header"><div class="row align-items-center"><div class="col"><h4 class="mb-0">Nenhum resultado encontrado.</h4></div></div></div></div></div>';
        }

        page_span.innerHTML = page + "/" + numPages();

        if (page == 1 || numPages() == 0) {
            btn_prev.style.visibility = "hidden";
        } else {
            btn_prev.style.visibility = "visible";
        }

        if (page == numPages()) {
            btn_next.style.visibility = "hidden";
        } else {
            btn_next.style.visibility = "visible";
        }
    }

    function numPages() {
        return Math.ceil(products.length / records_per_page);
    }

    window.onload = function () {
        changePage(1);
    };

    function shuffle(array) {
        var currentIndex = array.length, temporaryValue, randomIndex;

        // While there remain elements to shuffle...
        while (0 !== currentIndex) {

            // Pick a remaining element...
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;

            // And swap it with the current element.
            temporaryValue = array[currentIndex];
            array[currentIndex] = array[randomIndex];
            array[randomIndex] = temporaryValue;
        }

        return array;
    }

    function printProduct(product) {

        var truncatedTitle = product.title && product.title.length > 50 ? product.title.substring(0, 50) + "..." : product.title;

        var pricing_html = setPricing(product);

        var imgSrc = product.img_source_data
        ? `data:image/jpeg;base64,${product.img_source_data}`
        : '';
        
        var flag = product.products_from === 'CN' 
        ? '<div class="china-flag-container"><img src="' + "{{asset('assets/img/Flag_of_the_Peoples_Republic_of_China.svg')}}" + '" class="china-flag" /></div>'
        : '';


        var html = `
        <div class="col-xl-4 col-lg-6 col-12">
            <div class="product-card">
                <div class="product-card-header">
                    ${flag}
                    <img src="${imgSrc}" alt="Imagem do Produto" class="product-image">
                    <h2 class="product-title">${truncatedTitle}</h2>
                </div>
                <div class="product-card-body">
        <div class="product-pricing-left">
            <span class="price">${pricing_html}</span>
        </div>
        <div class="button-container">
            <a href="${window.location.origin + '/shop/products/details/' + product.hash}" class="btn btn-primary">Pré-visualizar</a>
        </div>
    </div>
            </div>
        </div>
    `;
        
        html = html.replace('#title#', (product.title).substr(0, 20) + '...');
        if (product.supplier.address) {
            html = html.replace('#city_state#', '');
        } else {
            html = html.replace('#city_state#', '');
        }

        if (product.img_source) {
            html = html.replace('#img_source#', product.img_source);
        } else {
            html = html.replace('#img_source#', '{{ asset('assets / img / products / eng - product - no - image.png') }}')
        }

        html = html.replace('#description', product.description);
        html = html.replace('#pricing#', pricing_html);
        html = html.replace('#route#', window.location.origin + '/shop/products/link?hash=' + product.hash);
        html = html.replace('#details_route#', window.location.origin + '/shop/products/details/' + product.hash);

        return html;
    }

    function setPricing(product) {
        variants = product.variants;
        var html = '#low_price# à #high_price#';
        var low_price = 0, high_price = 0;

        $.each(variants, function (key, variant) {
            if (low_price == 0 && high_price == 0) {
                low_price = variant.price;
                high_price = variant.price;
            }

            low_price = variant.price < low_price ? variant.price : low_price;
            high_price = variant.price > high_price ? variant.price : high_price;
        });

        if (low_price == high_price) {
            var html = '#low_price#';
        }

        if (product.currency == 'US$') {
            low_price = parseFloat(low_price).toLocaleString('pt-br', { style: 'currency', currency: 'USD' });
            high_price = parseFloat(high_price).toLocaleString('pt-br', { style: 'currency', currency: 'USD' })
        } else {
            low_price = parseFloat(low_price).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
            high_price = parseFloat(high_price).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })
        }


        html = html.replace('#low_price#', low_price);
        html = html.replace('#high_price#', high_price);

        return html;
    }

    function updateCountdown() {
        var deadline = new Date("2024-02-20T18:00:00").getTime();
        var now = new Date().getTime();
        var t = deadline - now;

        var days = Math.floor(t / (1000 * 60 * 60 * 24));
        var hours = Math.floor((t % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));

        var countdownDisplay = days + " dias " + hours + " horas " + minutes + " minutos ";
        document.getElementById("countdown").innerHTML = countdownDisplay;

        if (t < 0) {
            clearInterval(interval);
            document.getElementById("countdown").innerHTML = "O prazo expirou";
        }
    }

    var interval = setInterval(updateCountdown, 1000);

</script>
@endsection