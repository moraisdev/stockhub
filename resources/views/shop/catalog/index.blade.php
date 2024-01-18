@extends('shop.layout.default')

@section('title', config('app.name').' - Catálogo')

@section('stylesheets_before')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/js/slick/slick.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/js/slick/slick-theme.css') }}"/>

    <style>
        .selected_category{
            background-color: #2e43ba !important;
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
                                <input type="text" class="form-control float-right" name="search" id="search" placeholder="Pesquisar por nome" onkeyup="search()">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="slick mx-3 mt-3">
                                @foreach($categories as $category)
                                    <button type="button"  class="btn btn-primary btn-square category-button" category_id="{{ $category->id }}" onclick="selectCategory(this)">{{ $category->name }} </button>
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
                                <input type="checkbox" class="custom-control-input" id="products_from_brasil" name='products_from_brasil' checked>
                                <label class="custom-control-label" for="products_from_brasil" >Brasil</label>
                            </div>
                        </div>                        
                        <div class="col-1">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="products_from_china" name='products_from_china' checked>
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
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });

    var all_products = {!! $products !!};
    //console.log(all_products);
    var products = all_products;
    var current_page = 1;
    var records_per_page = 12;

    products = shuffle(products);

    $('#products_from_brasil').on('click', function(){
        search();
    })

    $('#products_from_china').on('click', function(){
        search();
    })

    function selectCategory(obj){
        current_page = 1
        $('.category-button').removeClass('selected_category');

        $(obj).addClass('selected_category');
        search();
    }

    function search(){
        let toSearch = $('#search').val();
        let categoryToSearch = $('.selected_category').attr('category_id');

        products = new Array();

        for(var i=0; i<all_products.length; i++) {
            let city_state = all_products[i].supplier.sku ? all_products[i].supplier.id.toLowerCase() + '/' + all_products[i].supplier.id.toLowerCase() : '';

             console.log(city_state)
            if(categoryToSearch && categoryToSearch != 'all'){
                if(all_products[i].category_id == categoryToSearch && (all_products[i].title.toLowerCase().indexOf(toSearch.toLowerCase()) != -1 || city_state.indexOf(toSearch.toLowerCase()) != -1)){
                    if($('#products_from_brasil').is(':checked') && all_products[i].products_from === 'BR'){
                        products.push(all_products[i]);
                    }

                    if($('#products_from_china').is(':checked') && all_products[i].products_from === 'CN'){
                        products.push(all_products[i]);
                    }
                }
            }else{
                if(all_products[i].title.toLowerCase().indexOf(toSearch.toLowerCase()) != -1 || city_state.indexOf(toSearch.toLowerCase()) != -1) {
                    if($('#products_from_brasil').is(':checked') && all_products[i].products_from === 'BR'){
                        products.push(all_products[i]);
                    }

                    if($('#products_from_china').is(':checked') && all_products[i].products_from === 'CN'){
                        products.push(all_products[i]);
                    }
                }
            }
        }

        changePage(1);
    }

    function prevPage()
    {
        if (current_page > 1) {
            current_page--;
            changePage(current_page);
        }
    }

    function nextPage()
    {
        if (current_page < numPages()) {
            current_page++;
            changePage(current_page);
        }
    }

    function changePage(page)
    {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var listing_table = document.getElementById("products-container");
        var page_span = document.getElementById("page");

        // Validate page
        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();

        listing_table.innerHTML = "";

        if(products.length > 0){
            for (var i = (page-1) * records_per_page; i < (page * records_per_page) && i < products.length; i++) {
                listing_table.innerHTML += printProduct(products[i]);
            }
        }else{
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

    function numPages()
    {
        return Math.ceil(products.length / records_per_page);
    }

    window.onload = function() {
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

    // #title# = Product Title
    // #img_source# = Product image source
    // #pricing# = Variants List
    // #route# = Add to catalog link
    function printProduct(product){
        var html = '<div class="col-xl-4 col-lg-6 col-12"><div class="card mt-4 shadow"><div class="card-header bg-transparent py-2">'+(product.products_from === 'CN' ? '<img src="{{asset('assets/img/Flag_of_the_Peoples_Republic_of_China.svg')}}" style="height: 22px; widht:22; position: absolute; right: 0.5rem; top: 0.5rem;" />' : '')+'<div class="row align-items-center"><div class="col"><h4 class="mb-0">#title#</h4></div></div></div><div class="card-body p-0"><div class="catalog-product-image-div" style="background-image: url(\'#img_source#\'); height: 330px !important; widht:100%; "></div><div class="p-2 mb-2"><div class="catalog-city-state text-info">#city_state#</div><div class="catalog-description"><small>#description</small></div><div class="catalog-pricing">#pricing#</div><a href="#route#" class="btn btn-sm btn-success btn-block mt-4">Adicionar ao carrinho</a><a href="#details_route#" target="_blank" class="btn btn-sm btn-primary btn-block">Detalhes</a></div></div></div></div>';

        var pricing_html = setPricing(product);

        //console.log(pricing_html);

        html = html.replace('#title#', (product.title).substr(0, 20)+'...');
        if(product.supplier.address){
            html = html.replace('#city_state#', '');
        }else{
            html = html.replace('#city_state#', '');
        }

        if(product.img_source){
            html = html.replace('#img_source#', product.img_source);
        }else{
            html = html.replace('#img_source#', '{{ asset('assets/img/products/eng-product-no-image.png') }}')
        }

        html = html.replace('#description', product.description);
        html = html.replace('#pricing#', pricing_html);
        html = html.replace('#route#', window.location.origin+'/shop/products/link?hash='+product.hash);
        html = html.replace('#details_route#', window.location.origin+'/shop/products/details/'+product.hash);

        return html;
    }

    //Fill:
    // #price# : Variant price
    // #title# : Variant title
    function setPricing(product){
        variants = product.variants;
        var html = '#low_price# à #high_price#';
        var low_price = 0, high_price = 0;

        $.each(variants, function(key, variant){
            if(low_price == 0 && high_price == 0){
                low_price = variant.price;
                high_price = variant.price;
            }

            low_price = variant.price < low_price ? variant.price : low_price;
            high_price = variant.price > high_price ? variant.price : high_price;
        });

        if(low_price == high_price){
            var html = '#low_price#';
        }

        if(product.currency == 'US$'){
            low_price = parseFloat(low_price).toLocaleString('pt-br', {style: 'currency', currency: 'USD'});
            high_price = parseFloat(high_price).toLocaleString('pt-br', {style: 'currency', currency: 'USD'})
        }else{
            low_price = parseFloat(low_price).toLocaleString('pt-br', {style: 'currency', currency: 'BRL'});
            high_price = parseFloat(high_price).toLocaleString('pt-br', {style: 'currency', currency: 'BRL'})
        }


        html = html.replace('#low_price#', low_price);
        html = html.replace('#high_price#', high_price);

        return html;
    }
</script>
@endsection
