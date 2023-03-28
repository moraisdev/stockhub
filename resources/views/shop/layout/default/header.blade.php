<!-- Navbar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="#">@yield('title')</a>
        <!-- Form
        <form class="navbar-search navbar-search-dark form-inline mr-3 d-none d-md-flex ml-lg-auto">
            <div class="form-group mb-0">
                <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input class="form-control" placeholder="Pesquisar" type="text">
                </div>
            </div>
        </form>
         -->
        <!-- User -->
        <ul class="navbar-nav align-items-center d-none d-md-flex">
            
                @include('shop.layout.default.header-menu')
            </li>
        </ul>
    </div>
</nav>
<!-- End Navbar -->