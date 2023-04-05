<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>{{config('app.name').' - Catálogo'}}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <!-- Favicon -->
    <link href="{{ asset('assets/img/brand/favicon.ico') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">  

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    <!-- Topbar Start -->
    <div class="container-fluid">
        <div class="row bg-secondary py-1 px-xl-5">
            <div class="col-lg-6 d-none d-lg-block">
                
            </div>
            <div class="col-lg-6 text-center text-lg-right">
                <div class="d-inline-flex align-items-center">
                    
                    
                    
                </div>
                <div class="d-inline-flex align-items-center d-block d-lg-none">
                      <img src="{{ asset('assets/img/brand/logo.png?v=2') }}" class="navbar-brand-img" width="130px"  alt="...">
                </div>
            </div>
        </div>
        <div class="row align-items-center bg-light py-3 px-xl-5 d-none d-lg-flex">
            <div class="col-lg-2">
                
                <a class="navbar-brand pt-0" href="{{ route('shop.dashboard') }}">
                  <img src="{{ asset('assets/img/brand/logo.png?v=2') }}" class="navbar-brand-img" width="130px"  alt="...">
                </a>    

                </a>
            </div>
            <div class="col-lg-4 col-6 text-left">
                <form action="">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Pesquisar Produto">
                        <div class="input-group-append">
                            <span class="input-group-text bg-transparent text-primary">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-4 col-6 text-right">
                
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <div class="container-fluid bg-sucess mb-30" style="background-color:{{ env('CATALOGCOR') }}">
        <div class="row px-xl-5">
            <div class="col-lg-3 d-none d-lg-block">
                <a class="btn d-flex align-items-center justify-content-between bg-primary w-100" data-toggle="collapse" href="#navbar-vertical" style="height: 65px; padding: 0 30px;">
                    <h6 class="text-dark m-0"><i class="fa fa-bars mr-2"></i>{{ trans('supplier.categorias') }}</h6>
                    <i class="fa fa-angle-down text-dark"></i>
                </a>
                <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 bg-light" id="navbar-vertical" style="width: calc(100% - 30px); z-index: 999;">
                    <div class="navbar-nav w-100">
                    @foreach($categories as $cat)
                        <a href="{{ route('shop.categoria', $cat->id) }}" class="nav-item nav-link">{{$cat->name}}</a>
                    @endforeach
                      </div>
                </nav>
            </div>
            
        </div>
    </div>
    <!-- Navbar End -->


    

    <!-- Products Start -->
    <div class="container-fluid pt-5 pb-3">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">Catálogo de Produtos</span></h2>
        <div class="row px-xl-5">
            
        @foreach($products as $prod)
              <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
                <div class="product-item bg-light mb-4">
                    <div class="product-img position-relative overflow-hidden">
                        <img class="img-fluid w-100" src="{{$prod->img_source}}" width="280px" height="300px" alt="">
                        <div class="product-action">
                            <a class="btn btn-outline-dark btn-square" href="{{ route('shop.catalogodetalhe', $prod->id) }}">
                                
                            <i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="text-center py-4">
                        <a class="h6 text-decoration-none text-truncate" href="">{{substr($prod->title, 0,33)."..."}}</a>
                        <div class="d-flex align-items-center justify-content-center mt-2">
                       
                        @if ($admins->price_catalog == 0)
						 @if(isset($prod->variants[0]->price))
                        <h5>R$ {{$prod->variants[0]->price}}</h5>
                            @else 
							 <h5>R$ Preço Sob Consulta </h5>
							
							@endif
							
                        @else 
                        <h5>R$ Preço Sob Consulta </h5>
                        @endif

                    </div>
                       
                        <div class="d-flex align-items-center justify-content-center mb-1">
                            <small class="fa fa-star text-primary mr-1"></small>
                            <small class="fa fa-star text-primary mr-1"></small>
                            <small class="fa fa-star text-primary mr-1"></small>
                            <small class="fa fa-star text-primary mr-1"></small>
                            <small class="fa fa-star text-primary mr-1"></small>
                           
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
        </div>
    </div>
    <!-- Products End -->



    <!-- Footer Start -->
    <div class="container-fluid bg-sucess text-secondary mt-5 pt-5" style="background-color:{{ env('CATALOGCOR') }}">
        
        <div class="row border-top mx-xl-5 py-4" style="border-color: rgba(256, 256, 256, .1) !important;">
            <div class="col-md-6 px-xl-0">
                <p class="mb-md-0 text-center text-md-left text-secondary">
     
                  &copy; 2020 <a href="{{config('app.terms_url')}}" class="font-weight-bold ml-1" target="_blank">{{config('app.name')}}</a>
     
                </p>
            </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
  
</body>

</html>