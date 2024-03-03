


<li class="nav-item dropdown">
    <a class="nav-link pr-0 d-none d-md-block" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
                <span class="avatar avatar-sm rounded-circle bg-white">
                    @if($authenticated_user->img_profile)
                        <img alt="Image placeholder" src="data:image/jpeg;base64,{{ $authenticated_user->img_profile }}">
                    @else
                        <img alt="Image placeholder" src="{{ asset('assets/img/products/eng-product-no-image.png') }}">
                    @endif
                </span>
                <div class="media-body ml-2 d-none d-lg-block">
                    <span class="mb-0 text-sm  font-weight-bold">{{ $authenticated_user->responsible_name }}</span>
                </div>
            </div>
    </a>
    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
        <div class=" dropdown-header noti-title">
            <h6 class="text-overflow m-0">Bem-vindo(a) !</h6>
        </div>
        <div class="dropdown-divider"></div>
        <a href="{{ route('shop.logout') }}" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</li>
<li>
    <a class="nav-link pr-0 d-none d-md-block" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell"></i>
    </a>
</li>