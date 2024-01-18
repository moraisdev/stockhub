<!-- Navbar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="#">@yield('title')</a>
        <!-- User -->
        <ul class="navbar-nav align-items-center d-none d-md-flex">
            <li>
                @include('admin.layout.default.header-menu')
            </li>
        </ul>
    </div>
</nav>
<!-- End Navbar -->