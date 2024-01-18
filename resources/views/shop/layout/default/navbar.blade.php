<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ route('shop.dashboard') }}">
            <img src="{{ asset('assets/img/brand/logo.png?v=2') }}" class="navbar-brand-img" alt="...">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            @include('shop.layout.default.header-menu')
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('shop.dashboard') }}">
                            <img src="{{ asset('assets/img/brand/logo.png?v=2') }}">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Alternar navegação">
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
                    <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('shop.dashboard') }}">
                        <i class="fas fa-chart-bar text-primary"></i> Dashboard
                    </a>
                </li>
                {{--<li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'partners') ? 'active' : '' }}" href="{{ route('shop.partners.index') }}">
                        <i class="fas fa-user text-info"></i> Meus fornecedores
                    </a>
                </li>--}}
                <li class="nav-item">
                    <a class="nav-link {{ ($uri_1 == 'orders' && $uri_2 != 'history') ? 'active' : '' }}" href="#orders-dropdown" data-toggle="collapse" data-target="#orders-dropdown" aria-controls="orders-dropdown" aria-expanded="true">
                        <i class="fas fa-pallet text-success" aria-hidden="true"></i> Pedidos
                    </a>
                    <div class="collapse {{ ($uri_1 == 'orders') ? 'show' : '' }}" id="orders-dropdown" style="">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 " href="{{ route('shop.orders.index') }}">
                                    <i class="fas fa-box-open text-info" style="font-size: 0.7rem; min-width: 1.5rem" aria-hidden="true"></i> Carrinho
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 " href="{{ route('shop.orders.paid_groups') }}">
                                    <i class="fas fa-dollar-sign" style="font-size: 0.7rem; min-width: 1.5rem" aria-hidden="true"></i> Pedidos pagos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 " href="{{ route('shop.orders.sent') }}">
                                    <i class="fas fa-shipping-fast text-warning" style="font-size: 0.7rem; min-width: 1.5rem" aria-hidden="true"></i> Enviados
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 " href="{{ route('shop.orders.completed') }}">
                                    <i class="fas fa-check-circle text-success" style="font-size: 0.7rem; min-width: 1.5rem" aria-hidden="true"></i> Entregues
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'returned')) ? 'active' : '' }}" href="{{ route('shop.orders.returned') }}">
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
                                <a class="nav-link py-1 {{ ($uri_1 == 'returns' && request()->status == 'pending') ? 'active' : '' }}" href="{{ route('shop.returns.index', ['status' => 'pending']) }}">
                                    <i class="fas fa-arrow-left text-warning" style="font-size: 0.7rem; min-width: 1.5rem"></i> Pendentes {!! ($pending_return_messages_count > 0) ? '<span class="badge badge-warning ml-1">'.$pending_return_messages_count.'</span>' : '' !!}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'returns' && request()->status == 'resolved') ? 'active' : '' }}" href="{{ route('shop.returns.index', ['status' => 'resolved']) }}">
                                    <i class="fas fa-check text-success" style="font-size: 0.7rem; min-width: 1.5rem"></i> Resolvidos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'catalog') ? 'active' : '' }}" href="{{ route('shop.catalog.index') }}">
                        <i class="fas fa-store text-warning"></i> Catálogo de produtos
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'simulador-frete') ? 'active' : '' }}" href="{{route('shop.freight-simulator.index')}}">
                        <i class="fas fa-calculator"></i> Simulador de Frete
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'profile') ? 'active' : '' }}" href="{{ route('shop.profile') }}">
                        <i class="fas fa-user-cog text-gray"></i> Meu Perfil
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'assinatura') ? 'active' : '' }}" href="{{ route('shop.plans.invoice') }}">
                        <i class="ni ni-ui-04 text-success"></i> Assinatura
                    </a>
                </li>
                
                
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'settings') ? 'active' : '' }}" href="{{ route('shop.settings.index') }}">
                        <i class="fas fa-cogs text-gray"></i> Integrações
                    </a>
                </li>
                
                <li class="nav-item">
                    
                    <a class="nav-link" href="https://api.whatsapp.com/send?phone={{ env('SUPPORT_WHATSAPP') }}&text=Ola%2C%20vim%20do%20SAC%20e%20gostaria%20de%20falar%20com%20a%20equipe%20de%20assist%C3%AAncia%20a%20Lojistas" target="_blank">
                        <i class="fab fa-whatsapp text-success"></i> Suporte
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'tutorials') ? 'active' : '' }}" href="{{ route('shop.tutorials.index') }}">
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
