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
                        <i class="fas fa-chart-bar text-primary"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'suppliers') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}">
                        <i class="fas fa-box text-info"></i> Fornecedores
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'shops') ? 'active' : '' }}" href="{{ route('admin.shops.index') }}">
                        <i class="fas fa-store text-red"></i> Lojistas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#orders-dropdown" data-toggle="collapse" data-target="#orders-dropdown" aria-controls="orders-dropdown" aria-expanded="{{ (($uri_1 == 'products' && $uri_2 != 'shopify_import') || $uri_1 == 'custom_links') ? 'true' : 'false' }}">
                        <i class="fas fa-pallet text-primary"></i> Pedidos
                    </a>
                    <div class="collapse {{ ($uri_1 == 'orders') ? 'show' : '' }}" id="orders-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && $uri_2 == 'shops') ? 'active' : '' }}" href="{{ route('admin.orders.shops') }}">
                                    <i class="fas fa-store text-warning" style="font-size: 0.7rem; min-width: 1.5rem"></i> Lojistas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && $uri_2 == 'suppliers') ? 'active' : '' }}" href="{{ route('admin.orders.suppliers') }}">
                                    <i class="fas fa-box text-warning" style="font-size: 0.7rem; min-width: 1.5rem"></i> Fornecedores
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'categories') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-tag text-success"></i> Categorias
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#banners-dropdown" data-toggle="collapse" data-target="#banners-dropdown" aria-controls="banners-dropdown" aria-expanded="{{ ($uri_1 == 'banners') ? 'true' : 'false' }}">
                        <i class="fab fa-slideshare"></i> Banners
                    </a>
                    <div class="collapse {{ ($uri_1 == 'banners') ? 'show' : '' }}" id="banners-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'banners') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                                    <i class="fab fa-slideshare text-warning" style="font-size: 0.7rem; min-width: 1.5rem"></i> Lojistas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'banners') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                                    <i class="fab fa-slideshare text-warning" style="font-size: 0.7rem; min-width: 1.5rem"></i> Fornecedores
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'settings') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cogs text-gray"></i> Configurações
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'assinaturapaid') ? 'active' : '' }}" href="{{ route('admin.plans.paid') }}">
                        <i class="fas fa-dollar-sign text-success"></i> Assinatura Pagas
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link collapsed" href="#assinatura-dropdown" data-toggle="collapse" data-target="#assinatura-dropdown" aria-controls="assinatura-dropdown" aria-expanded="{{ ($uri_1 == 'assinatura') ? 'true' : 'false' }}">
                        <i class="ni ni-ui-04 text-success"></i> Assinaturas
                    </a>
                    <div class="collapse {{ ($uri_1 == 'assinatura') ? 'show' : '' }}" id="assinatura-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'assinatura') ? 'active' : '' }}" href="{{ route('admin.planos.index') }}">
                                    <i class="ni ni-bullet-list-67 text-warning" style="font-size: 0.7rem; min-width: 1.5rem"></i> Configurar Planos
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'pagamentos') ? 'active' : '' }}" href="{{ route('admin.plans.payconfig') }}">
                                    <i class="ni ni-credit-card text-success" style="font-size: 0.7rem; min-width: 1.5rem"></i> Configurar Pagamento
                                </a>
                            </li>

                            

                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                              <a class=" nav-link {{ ($uri_1 == 'affiliate-link') ? 'active' : '' }}" href="{{ route('admin.affiliate-link.index') }}">
                                  <i class="fas fa-link"></i> Links Afiliados
                              </a>
                </li>
               
              
                

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'Tutorial') ? 'active' : '' }}" href="{{ route('admin.tutorial.index') }}">
                        <i class="fas fa-chalkboard-teacher"></i> Tutorial
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'Email') ? 'active' : '' }}" href="{{ route('admin.emailtemplate.index') }}">
                        <i class="fa fa-envelope-open-o"></i> Email Template
                    </a>
                </li>


            </ul>
        </div>
    </div>
</nav>
