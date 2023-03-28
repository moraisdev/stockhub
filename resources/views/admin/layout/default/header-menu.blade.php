<li class="nav-item dropdown">
    <a class="nav-link pr-0 d-none d-md-block" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="media align-items-center">
            <span class="avatar avatar-sm rounded-circle bg-white">
                <img alt="Image placeholder" src="{{ asset('assets/img/products/eng-product-no-image.png') }}">
            </span>
            <div class="media-body ml-2 d-none d-lg-block">
                <span class="mb-0 text-sm  font-weight-bold">{{ $authenticated_user->name }}</span>
            </div>
        </div>
    </a>
    <a class="nav-link d-md-none" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="media align-items-center">
            <span class="avatar avatar-sm rounded-circle">
                <img alt="Image placeholder" src="{{ asset('assets/img/products/eng-product-no-image.png') }}">
            </span>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
        <div class=" dropdown-header noti-title">
            <h6 class="text-overflow m-0">Seja bem vindo!</h6>
        </div>
        <div class="dropdown-divider"></div>
        <a href="{{ route('admin.logout') }}" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</li>