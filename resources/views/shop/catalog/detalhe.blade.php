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
                        <a href="" class="nav-item nav-link">{{$cat->name}}</a>
                    @endforeach
                      </div>
                </nav>
            </div>
            
        </div>
    </div>
    <!-- Navbar End -->


    

    <!-- Products Start -->
    <div class="col-lg-12 col-md-12 col-sm-12 pb-1">
                <div class="product-item bg-light mb-4">

        <div class="product-detail-top">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                               
                                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block" src="{{$products->img_source}}" alt="First slide">
    </div>
    @foreach($productimg as $img)
    <div class="carousel-item">
      <img class="d-block" src="{{$img->src}}" alt="Third slide">
    </div>
    @endforeach
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Anterior</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Proximo</span>
  </a>
</div>



                                </div>
                                <div class="col-md-7">
                                    <div class="product-content">
                                        <div class="title"><h2>{{$products->title}}</h2></div>
                                        <div class="ratting">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div class="price">
                                             @if ($admins->price_catalog == 0)
                                            <h4>R$ {{$productvar->price}}</h4>
                                            @else 
                                            <h4>R$ Preço Sob Consulta </h4>
                                            @endif
                                            <p></p>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
            
      



           
            
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