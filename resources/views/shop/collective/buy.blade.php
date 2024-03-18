@extends('shop.layout.default')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Confirm Your Order</div>

                <div class="card-body">
                    <p>Please review your order and proceed to payment.</p>
                    <ul>
                        <li>Order ID: {{ $collective->id }}</li>
                        <li>Cost: R$ {{ number_format($collective->cost_price, 2) }}</li>
                        <li>Description: {{ $collective->description }}</li>
                    </ul>

                    <form action="{{ route('collective.buy', $collective->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
