<style>
.icon-size {
    width: 20px;
    height: 20px;
    margin-right: 15px;
}
.subicon-size {
    width: 15px;
    height: 15px;
    margin-right: 8px;
}
.navbar-brand-img {
    margin-left: auto;
    margin-right: auto;
    margin-bottom: -40px;
}

</style>

<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Abrir navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/img/brand/logo.png?v=2') }}" class="navbar-brand-img" alt="...">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            @include('admin.layout.default.header-menu')
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('admin.dashboard') }}">
                            <img src="{{ asset('assets/img/brand/logo.png?v=2') }}">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Abrir navegação">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Form -->
            {{-- <form class="mt-4 mb-3 d-md-none">
                <div class="input-group input-group-rounded input-group-merge">
                    <input type="search" class="form-control form-control-rounded form-control-prepended" placeholder="Search" aria-label="Search">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-search"></span>
                        </div>
                    </div>
                </div>
            </form> --}}
            <!-- Navigation -->
            @php
                $uri_1 = request()->segment(2);
                $uri_2 = request()->segment(3);
            @endphp
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('assets/img/chart-line-up.svg') }}" class="icon-size"> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'suppliers') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}">
                        <img src="{{ asset('assets/img/cart-flatbed-boxes.svg') }}" class="icon-size"> Fornecedores
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'shops') ? 'active' : '' }}" href="{{ route('admin.shops.index') }}">
                    <img src="{{ asset('assets/img/store.svg') }}" class="icon-size"> Lojistas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#orders-dropdown" data-toggle="collapse" data-target="#orders-dropdown" aria-controls="orders-dropdown" aria-expanded="{{ (($uri_1 == 'products' && $uri_2 != 'shopify_import') || $uri_1 == 'custom_links') ? 'true' : 'false' }}">
                        <img src="{{ asset('assets/img/basket-shopping.svg') }}" class="icon-size"> Pedidos
                    </a>
                    <div class="collapse {{ ($uri_1 == 'orders') ? 'show' : '' }}" id="orders-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && $uri_2 == 'shops') ? 'active' : '' }}" href="{{ route('admin.orders.shops') }}">
                                    <img src="{{ asset('assets/img/store.svg') }}" class="subicon-size"> Lojistas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && $uri_2 == 'suppliers') ? 'active' : '' }}" href="{{ route('admin.orders.suppliers') }}">
                                    <img src="{{ asset('assets/img/cart-flatbed-boxes.svg') }}" class="subicon-size">  Fornecedores
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'categories') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <img src="{{ asset('assets/img/tag.svg') }}" class="icon-size"> Categorias
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#banners-dropdown" data-toggle="collapse" data-target="#banners-dropdown" aria-controls="banners-dropdown" aria-expanded="{{ ($uri_1 == 'banners') ? 'true' : 'false' }}">
                        <img src="{{ asset('assets/img/screen-users.svg') }}" class="icon-size"> Banners
                    </a>
                    <div class="collapse {{ ($uri_1 == 'banners') ? 'show' : '' }}" id="banners-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'banners') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                                    <img src="{{ asset('assets/img/store.svg') }}" class="subicon-size">  Lojistas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'banners') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                                    <img src="{{ asset('assets/img/cart-flatbed-boxes.svg') }}" class="subicon-size">  Fornecedores
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'settings') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <img src="{{ asset('assets/img/gear.svg') }}" class="icon-size"> Configurações
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'assinaturapaid') ? 'active' : '' }}" href="{{ route('admin.plans.paid') }}">
                        <img src="{{ asset('assets/img/circle-dollar.svg') }}" class="icon-size"> Assinatura Pagas
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link collapsed" href="#assinatura-dropdown" data-toggle="collapse" data-target="#assinatura-dropdown" aria-controls="assinatura-dropdown" aria-expanded="{{ ($uri_1 == 'assinatura') ? 'true' : 'false' }}">
                        <img src="{{ asset('assets/img/money-check-pen.svg') }}" class="icon-size"> Assinaturas
                    </a>
                    <div class="collapse {{ ($uri_1 == 'assinatura') ? 'show' : '' }}" id="assinatura-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'assinatura') ? 'active' : '' }}" href="{{ route('admin.planos.index') }}">
                                    <img src="{{ asset('assets/img/gears.svg') }}" class="subicon-size"> Configurar Planos
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'pagamentos') ? 'active' : '' }}" href="{{ route('admin.plans.payconfig') }}">
                                    <img src="{{ asset('assets/img/envelope-open-dollar.svg') }}" class="subicon-size"> Configurar Pagamento
                                </a>
                            </li>

                            

                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                              <a class=" nav-link {{ ($uri_1 == 'affiliate-link') ? 'active' : '' }}" href="{{ route('admin.affiliate-link.index') }}">
                                <img src="{{ asset('assets/img/link.svg') }}" class="icon-size"> Links Afiliados
                              </a>
                </li>
               
              
                

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'Tutorial') ? 'active' : '' }}" href="{{ route('admin.tutorial.index') }}">
                    <img src="{{ asset('assets/img/display.svg') }}" class="icon-size"> Tutorial
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'Email') ? 'active' : '' }}" href="{{ route('admin.emailtemplate.index') }}">
                        <img src="{{ asset('assets/img/envelope.svg') }}" class="icon-size"> Email Template
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'institucional') ? 'active' : '' }}" href="{{ route('admin.institucional') }}">
                    <img src="{{ asset('assets/img/building.svg') }}" class="icon-size"> Site Institucional
                    </a>
                </li>


            </ul>
        </div>
    </div>
</nav>
