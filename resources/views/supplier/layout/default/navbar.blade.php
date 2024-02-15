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
                        <i class="fas fa-chart-bar text-primary"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'partners') ? 'active' : '' }}" href="{{ route('supplier.partners.index') }}">
                        <i class="fas fa-user text-info"></i> Parceiros
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'products') ? 'active' : '' }}" href="{{ route('supplier.products.index') }}">
                        <i class="fas fa-box text-red"></i> Produtos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#orders-dropdown" data-toggle="collapse" data-target="#orders-dropdown" aria-controls="orders-dropdown" aria-expanded="{{ (($uri_1 == 'products' && $uri_2 != 'shopify_import') || $uri_1 == 'custom_links') ? 'true' : 'false' }}">
                        <i class="fas fa-pallet text-success"></i> Pedidos
                    </a>
                    <div class="collapse {{ ($uri_1 == 'orders') ? 'show' : '' }}" id="orders-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'pending')) ? 'active' : '' }}" href="{{ route('supplier.orders.index', ['status' => 'pending']) }}">
                                    <i class="fas fa-box-open text-info" style="font-size: 0.7rem; min-width: 1.5rem"></i> Pendentes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'sent')) ? 'active' : '' }}" href="{{ route('supplier.orders.index', ['status' => 'sent']) }}">
                                    <i class="fas fa-shipping-fast text-warning" style="font-size: 0.7rem; min-width: 1.5rem"></i> Enviados
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'completed')) ? 'active' : '' }}" href="{{ route('supplier.orders.index', ['status' => 'completed']) }}">
                                    <i class="fas fa-dolly text-success" style="font-size: 0.7rem; min-width: 1.5rem"></i> Entregues
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'returned')) ? 'active' : '' }}" href="{{ route('supplier.orders.index', ['status' => 'returned']) }}">
                                    <i class="fas fa-undo text-primary" style="font-size: 0.7rem; min-width: 1.5rem"></i> Devolvidos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#returns-dropdown" data-toggle="collapse" data-target="#returns-dropdown" aria-controls="returns-dropdown" aria-expanded="{{ ($uri_1 == 'returns') ? 'true' : 'false' }}">
                        <i class="fas fa-undo text-warning"></i> Reembolsos {!! ($pending_return_messages_count > 0) ? '<span class="badge badge-warning ml-1">'.$pending_return_messages_count.'</span>' : '' !!}
                    </a>
                    <div class="collapse {{ ($uri_1 == 'returns') ? 'show' : '' }}" id="returns-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'returns' && request()->status == 'pending') ? 'active' : '' }}" href="{{ route('supplier.returns.index', ['status' => 'pending']) }}">
                                    <i class="fas fa-arrow-left text-warning" style="font-size: 0.7rem; min-width: 1.5rem"></i> Pendentes {!! ($pending_return_messages_count > 0) ? '<span class="badge badge-warning ml-1">'.$pending_return_messages_count.'</span>' : '' !!}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'returns' && request()->status == 'resolved') ? 'active' : '' }}" href="{{ route('supplier.returns.index', ['status' => 'resolved']) }}">
                                    <i class="fas fa-check text-success" style="font-size: 0.7rem; min-width: 1.5rem"></i> Resolvidos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'profile') ? 'active' : '' }}" href="{{ route('supplier.profile') }}">
                        <i class="fas fa-user-cog text-gray"></i> Meu Perfil
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'assinatura') ? 'active' : '' }}" href="{{ route('supplier.plans.invoice') }}">
                        <i class="ni ni-ui-04 text-success"></i> Assinatura
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'settings' && $uri_2 != 'discounts') ? 'active' : '' }}" href="{{ route('supplier.settings.index') }}">
                        <i class="fas fa-cogs text-gray"></i> Configurações
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_2 == 'discounts') ? 'active' : '' }}" href="{{ route('supplier.settings.discounts') }}">
                        <i class="fas fa-sticky-note text-gray"></i> Descontos
                    </a>
                </li>
                {{--<li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'settings') ? 'active' : '' }}" href="{{ route('supplier.settings.index') }}">
                        <i class="fas fa-cogs text-gray"></i> Configurações
                    </a>
                </li>--}}
                <li class="nav-item">
                    <a class="nav-link" href="https://api.whatsapp.com/send?phone={{ env('SUPPORT_WHATSAPP') }}&text=Ola%2C%20vim%20do%20SAC%20e%20gostaria%20de%20falar%20com%20a%20equipe%20de%20assist%C3%AAncia%20a%20Lojistas" target="_blank">
                        <i class="fab fa-whatsapp text-success"></i> Suporte
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'tutorials') ? 'active' : '' }}" href="{{ route('supplier.tutorials.index') }}">
                        <i class="fas fa-chalkboard-teacher"></i> Tutoriais
                    </a>
                </li>
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
