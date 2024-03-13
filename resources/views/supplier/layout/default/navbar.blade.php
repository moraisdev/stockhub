<style>
.icon-size {
    width: 20px;
    height: 20px;
    margin-right: 15px;
}
.subicon-size {
    width: 14px;
    height: 14px;
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
        <a class="navbar-brand pt-0" href="{{ route('supplier.dashboard') }}">
            <img src="{{ asset('assets/img/brand/logo.png?v=2') }}" class="navbar-brand-img" alt="...">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            @include('supplier.layout.default.header-menu')
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('supplier.dashboard') }}">
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
            @php
                $uri_1 = request()->segment(2);
                $uri_2 = request()->segment(3);
            @endphp
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('supplier.dashboard') }}">
                    <img src="{{ asset('assets/img/chart-line-up.svg') }}" class="icon-size"> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('shop.catalog.index') }}">
                        <img src="{{ asset('assets/img/radar.svg') }}" class="icon-size"> Radar Siscomex
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('supplier.collective.index') }}">
                        <img src="{{ asset('assets/img/users.svg') }}" class="icon-size"> Importação Coletiva
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'partners') ? 'active' : '' }}" href="{{ route('supplier.partners.index') }}">
                    <img src="{{ asset('assets/img/user.svg') }}" class="icon-size"> Clientes
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'products') ? 'active' : '' }}" href="{{ route('supplier.products.index') }}">
                    <img src="{{ asset('assets/img/box.svg') }}" class="icon-size"> Produtos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#orders-dropdown" data-toggle="collapse" data-target="#orders-dropdown" aria-controls="orders-dropdown" aria-expanded="{{ (($uri_1 == 'products' && $uri_2 != 'shopify_import') || $uri_1 == 'custom_links') ? 'true' : 'false' }}">
                    <img src="{{ asset('assets/img/basket-shopping.svg') }}" class="icon-size"> Pedidos
                    </a>
                    <div class="collapse {{ ($uri_1 == 'orders') ? 'show' : '' }}" id="orders-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'pending')) ? 'active' : '' }}" href="{{ route('supplier.orders.index', ['status' => 'pending']) }}">
                                <img src="{{ asset('assets/img/clock.svg') }}" class="subicon-size"> Pendentes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'sent')) ? 'active' : '' }}" href="{{ route('supplier.orders.index', ['status' => 'sent']) }}">
                                <img src="{{ asset('assets/img/truck-fast.svg') }}" class="subicon-size">  Enviados
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'completed')) ? 'active' : '' }}" href="{{ route('supplier.orders.index', ['status' => 'completed']) }}">
                                <img src="{{ asset('assets/img/box-check.svg') }}" class="subicon-size">  Entregues
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'returned')) ? 'active' : '' }}" href="{{ route('supplier.orders.index', ['status' => 'returned']) }}">
                                <img src="{{ asset('assets/img/arrow-right-arrow-left.svg') }}" class="subicon-size"> Devolvidos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#returns-dropdown" data-toggle="collapse" data-target="#returns-dropdown" aria-controls="returns-dropdown" aria-expanded="{{ ($uri_1 == 'returns') ? 'true' : 'false' }}">
                    <img src="{{ asset('assets/img/arrow-rotate-left.svg') }}" class="icon-size">Reembolsos {!! ($pending_return_messages_count > 0) ? '<span class="badge badge-warning ml-1">'.$pending_return_messages_count.'</span>' : '' !!}
                    </a>
                    <div class="collapse {{ ($uri_1 == 'returns') ? 'show' : '' }}" id="returns-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'returns' && request()->status == 'pending') ? 'active' : '' }}" href="{{ route('supplier.returns.index', ['status' => 'pending']) }}">
                                    <img src="{{ asset('assets/img/circle-exclamation.svg') }}" class="subicon-size"> Pendentes {!! ($pending_return_messages_count > 0) ? '<span class="badge badge-warning ml-1">'.$pending_return_messages_count.'</span>' : '' !!}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'returns' && request()->status == 'resolved') ? 'active' : '' }}" href="{{ route('supplier.returns.index', ['status' => 'resolved']) }}">
                                <img src="{{ asset('assets/img/circle-check.svg') }}" class="subicon-size"> Resolvidos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'profile') ? 'active' : '' }}" href="{{ route('supplier.profile') }}">
                    <img src="{{ asset('assets/img/user-gear.svg') }}" class="icon-size"> Meu Perfil
                    </a>
                </li>
 
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'settings' && $uri_2 != 'discounts') ? 'active' : '' }}" href="{{ route('supplier.settings.index') }}">
                    <img src="{{ asset('assets/img/gear.svg') }}" class="icon-size"> Configurações
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_2 == 'discounts') ? 'active' : '' }}" href="{{ route('supplier.settings.discounts') }}">
                    <img src="{{ asset('assets/img/badge-percent.svg') }}" class="icon-size"> Cupons
                    </a>
                </li>
                {{--<li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'settings') ? 'active' : '' }}" href="{{ route('supplier.settings.index') }}">
                        <i class="fas fa-cogs text-gray"></i> Configurações
                    </a>
                </li>--}}
                
                {{-- <li class="nav-item">
                    <div class='bg-secondary p-4 text-center'>
                        <a href="https://www.facebook.com/mawapost/" target='_blank'><i  style='font-size: 18pt !important; color: #979797; width: 40px; ' class="fab fa-facebook-square"></i></a>
                        <a href="https://www.instagram.com/mawapost/" target='_blank'><i  style='font-size: 18pt !important; color: #979797; width: 40px;' class="fab fa-instagram"></i></a>
                        <a href="https://www.youtube.com/channel/UCSw3Vvl8w_gJTuPyunXHbVg" target='_blank'><i  style='font-size: 18pt !important; color: #979797; width: 40px;' class="fab fa-youtube"></i></a>
                    </div>
                </li> --}}
            </ul>
            
            
        </div>
    </div>
</nav>
