<html>
<head>
    <title>Alteração rápida de produtos</title>
    <link href="{{ asset('assets/css/argon-dashboard.css?v=1.1.1') }}" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/7b818e6d8e.js" crossorigin="anonymous"></script>

    <style>
        .table tbody tr td{
            vertical-align: middle;
        }
    </style>
</head>
<body>
<form method="POST" action="{{ route('admin.products.update_descriptions.post', $supplier_id) }}">
    @csrf
    <div class="row mx-1 my-2">
        <div class="col">
            <h3 class="d-inline">Alteração rápida de produtos</h3>
            <div class="d-inline flex-grow-1">
                <div class="float-right">
                    @if(session('success'))
                        <span class="badge badge-success">{{ session('success') }}</span>
                    @endif
                    <button class="btn btn-primary btn-sm">Concluir alterações</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-default btn-sm">Voltar para o sistema</a>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-bordered">
        <thead class="bg-white">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Descrição</th>
        </tr>
        </thead>
        <tbody id="products-table">
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->title }}</td>
                    <td><textarea name="products[{{ $product->id }}]" class="form-control" rows="2" placeholder="Descrição do produto">{{ $product->description }}</textarea></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</form>
</body>
</html>
