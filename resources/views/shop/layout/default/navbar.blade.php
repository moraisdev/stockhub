
<style>
.icon-size {
    width: 12px;
    height: 12px;
    margin-right: 12px;
}

.subicon-size {
    width: 12px;
    height: 12px;
    margin-right: 8px;
}

.navbar-brand-img {
    margin-left: auto;
    margin-right: auto;
    margin-bottom: -40px;
    height: 100%;
}

.navbar {
    font-family: 'Poppins', sans-serif;
    background-color: #fff;
    color: #333;
}

.nav-link:hover {
    color: #555;
}

.coming-soon {
    opacity: 0.5;
    cursor: not-allowed;
}

.coming-soon:hover {
    opacity: 1;
}

.icon-box {
    width: 38px; /* Adjust to match the size you want */
    height: 38px; /* Same as width to make it square */
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px; /* Rounded corners */
    background: linear-gradient(135deg, #e6e6e6 50%, #F4F7FC 100%, #e6e6e6 50%);
    box-shadow: 5px 5px 10px #c8c8c8, -5px -5px 10px #ffffff; /* Inset shadow for the "lifted" effect */
}


.navbar-vertical {
  position: fixed;
  top: 0;
  left: 0;
  width: 250px;
  height: 100%;
  background-color: #fff;
  padding-top: 20px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Adjusted for a subtle shadow */
  border-radius: 0; /* Remove border-radius for square edges */
}
</style>

<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
            <img src="{{ asset('assets/img/brand/logo.png?v=2') }}" class="navbar-brand-img" alt="...">
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
            <br/>
            <hr class="horizontal dark mt-0">
            @php
                $uri_1 = request()->segment(2);
                $uri_2 = request()->segment(3);
            @endphp
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'catalog') ? 'active' : '' }}" href="{{ route('shop.catalog.index') }}">
                        <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/img/bag-shopping.svg') }}" width="12px" height="12px">
                        </div>
                        <span class="nav-link-text ms-1"> Loja de Importação</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'collective') ? 'active' : '' }}" href="{{ route('shop.collective.index') }}">
                        <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/img/handshake.svg') }}" width="12px" height="12px">
                        </div>
                        <span class="nav-link-text ms-1"> Container Coletivo</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'private') ? 'active' : '' }}" href="{{ route('shop.private.index') }}">
                    <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/img/ship.svg') }}" width="12px" height="12px">
                    </div>
                    <span class="nav-link-text ms-1"> Container Privado</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class=" nav-link {{ ($uri_1 == 'radar') ? 'active' : '' }}" href="{{ route('shop.radar.index') }}">
                    <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/img/radar.svg') }}" width="12px" height="12px">
                    </div>
                    <span class="nav-link-text ms-1"> Radar Siscomex</span>
                    </a>
                </li>
                <li class="nav-item coming-soon" data-toggle="tooltip" data-placement="right" title="Em Breve">
                    <a class="nav-link">
                    <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/img/users.svg') }}" width="12px" height="12px">
                    </div>
                    <span class="nav-link-text ms-1"> Compra Coletiva</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($uri_1 == 'orders' && $uri_2 != 'history') ? 'active' : '' }}" href="#orders-dropdown" data-toggle="collapse" data-target="#orders-dropdown" aria-controls="orders-dropdown" aria-expanded="true">
                    <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/img/basket-shopping.svg') }}" width="12px" height="12px">
                    </div>
                    <span class="nav-link-text ms-1"> Pedidos</span>
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
                    <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">    
                        <img src="{{ asset('assets/img/arrow-rotate-left.svg') }}" width="12px" height="12px">
                    </div>
                    <span class="nav-link-text ms-1"> Reembolsos {!! ($pending_return_messages_count > 0) ? '<span class="badge badge-warning ml-1">'.$pending_return_messages_count.'</span>' : '' !!}</span>
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
                    <a class="nav-link {{ ($uri_1 == '' || $uri_1 == 'profile') ? 'active' : '' }}" href="#config-dropdown" data-toggle="collapse" data-target="#config-dropdown" aria-controls="config-dropdown" aria-expanded="{{ ($uri_1 == 'configuracoes' || $uri_1 == 'profile') ? 'true' : 'false' }}">
                    <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">    
                        <img src="{{ asset('assets/img/gear.svg') }}" width="12px" height="12px">
                    </div>
                    <span class="nav-link-text ms-1"> Configurações</span>
                    </a>
                    <div class="collapse {{ ($uri_1 == 'profile' || $uri_1 == 'settings') ? 'show' : '' }}" id="config-dropdown">
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('shop.profile') }}">
                                    <img src="{{ asset('assets/img/user-gear.svg') }}" class="subicon-size"> Perfil
                                </a>
                            </li>
                        </ul>
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class="nav-link py-1 {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('shop.profile.business') }}">
                                    <img src="{{ asset('assets/img/briefcase.svg') }}" class="subicon-size"> Negócios
                                </a>
                            </li>
                        </ul>
                        <ul class="flex-column nav">
                            <li class="nav-item">
                                <a class=" nav-link {{ ($uri_1 == '') ? 'active' : '' }}" href="{{ route('shop.settings.index') }}">
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
                    <div class="icon-box icon-size icon-shape icon-sm shadow border-box-md bg text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/img/chalkboard-user.svg') }}" width="12px" height="12px">
                    </div>
                    <span class="nav-link-text ms-1"> Tutoriais</span>
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
