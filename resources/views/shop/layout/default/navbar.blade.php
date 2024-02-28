
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
.coming-soon {
    opacity: 0.5;
    cursor: not-allowed;
}

.coming-soon:hover {
    opacity: 1;
}

</style>

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
                    <a class=" nav-link {{ ($uri_1 == 'catalog') ? 'active' : '' }}" href="{{ route('shop.catalog.index') }}">
                        <img src="{{ asset('assets/img/bag-shopping.svg') }}" class="icon-size"> Loja de Importação
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('shop.collective.index') }}">
                        <img src="{{ asset('assets/img/handshake.svg') }}" class="icon-size"> Container Coletivo
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('shop.private.index') }}">
                        <img src="{{ asset('assets/img/ship.svg') }}" class="icon-size"> Container Privado
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('shop.radar.index') }}">
                        <img src="{{ asset('assets/img/radar.svg') }}" class="icon-size"> Radar Siscomex
                    </a>
                </li>
                <li class="nav-item coming-soon" data-toggle="tooltip" data-placement="right" title="Em Breve">
                    <a class="nav-link">
                        <img src="{{ asset('assets/img/users.svg') }}" class="icon-size"> Compra Coletiva
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($uri_1 == 'orders' && $uri_2 != 'history') ? 'active' : '' }}" href="#orders-dropdown" data-toggle="collapse" data-target="#orders-dropdown" aria-controls="orders-dropdown" aria-expanded="true">
                        <img src="{{ asset('assets/img/basket-shopping.svg') }}" class="icon-size"> Pedidos
                    </a>
                    <div class="collapse {{ ($uri_1 == 'orders') ? 'show' : '' }}" id="orders-dropdown" style="">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 " href="{{ route('shop.orders.index') }}">
                                    <img src="{{ asset('assets/img/cart-shopping.svg') }}" class="subicon-size"> Carrinho
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 " href="{{ route('shop.orders.paid_groups') }}">
                                    <img src="{{ asset('assets/img/circle-dollar.svg') }}" class="subicon-size"> Pedidos pagos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 " href="{{ route('shop.orders.sent') }}">
                                    <img src="{{ asset('assets/img/truck-fast.svg') }}" class="subicon-size">  Enviados
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 " href="{{ route('shop.orders.completed') }}">
                                    <img src="{{ asset('assets/img/box-check.svg') }}" class="subicon-size">  Entregues
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'orders' && (isset($shipping_status) && $shipping_status == 'returned')) ? 'active' : '' }}" href="{{ route('shop.orders.returned') }}">
                                    <img src="{{ asset('assets/img/arrow-right-arrow-left.svg') }}" class="subicon-size"> Devolvidos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#returns-dropdown" data-toggle="collapse" data-target="#returns-dropdown" aria-controls="returns-dropdown" aria-expanded="{{ ($uri_1 == 'returns') ? 'true' : 'false' }}">
                        <img src="{{ asset('assets/img/arrow-rotate-left.svg') }}" class="icon-size"> Reembolsos {!! ($pending_return_messages_count > 0) ? '<span class="badge badge-warning ml-1">'.$pending_return_messages_count.'</span>' : '' !!}
                    </a>
                    <div class="collapse {{ ($uri_1 == 'returns') ? 'show' : '' }}" id="returns-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'returns' && request()->status == 'pending') ? 'active' : '' }}" href="{{ route('shop.returns.index', ['status' => 'pending']) }}">
                                    <img src="{{ asset('assets/img/circle-exclamation.svg') }}" class="subicon-size"> Pendentes {!! ($pending_return_messages_count > 0) ? '<span class="badge badge-warning ml-1">'.$pending_return_messages_count.'</span>' : '' !!}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'returns' && request()->status == 'resolved') ? 'active' : '' }}" href="{{ route('shop.returns.index', ['status' => 'resolved']) }}">
                                    <img src="{{ asset('assets/img/circle-check.svg') }}" class="subicon-size"> Resolvidos
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($uri_1 == 'configuracoes' || $uri_1 == 'profile') ? 'active' : '' }}" href="#config-dropdown" data-toggle="collapse" data-target="#config-dropdown" aria-controls="config-dropdown" aria-expanded="{{ ($uri_1 == 'configuracoes' || $uri_1 == 'profile') ? 'true' : 'false' }}">
                        <img src="{{ asset('assets/img/gear.svg') }}" class="icon-size"> Configurações
                    </a>
                    <div class="collapse {{ ($uri_1 == 'configuracoes' || $uri_1 == 'profile') ? 'show' : '' }}" id="config-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'profile') ? 'active' : '' }}" href="{{ route('shop.profile') }}">
                                    <img src="{{ asset('assets/img/user-gear.svg') }}" class="subicon-size"> Perfil
                                </a>
                            </li>
                        </ul>
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == 'business') ? 'active' : '' }}" href="{{ route('shop.profile.business') }}">
                                    <img src="{{ asset('assets/img/briefcase.svg') }}" class="subicon-size"> Negócios
                                </a>
                            </li>
                        </ul>
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class=" nav-link {{ ($uri_1 == 'settings') ? 'active' : '' }}" href="{{ route('shop.settings.index') }}">
                                    <img src="{{ asset('assets/img/code-fork.svg') }}" class="subicon-size"> Integrações
                                </a>
                            </li>
                        </ul>
                        <ul class="flex-column nav">
                            <li class="nav-item">
                            <a class="nav-link" href="https://api.whatsapp.com/send?phone={{ env('SUPPORT_WHATSAPP') }}&text=Ola%2C%20vim%20do%20SAC%20e%20gostaria%20de%20falar%20com%20a%20equipe%20de%20assist%C3%AAncia%20a%20Lojistas" target="_blank">
                                <img src="{{ asset('assets/img/whatsapp-svgrepo-com.svg') }}" class="subicon-size"> Suporte
                            </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'tutorials') ? 'active' : '' }}" href="{{ route('shop.tutorials.index') }}">
                        <img src="{{ asset('assets/img/chalkboard-user.svg') }}" class="icon-size"> Tutoriais
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
@section('scripts')
<script>

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>
@endsection
