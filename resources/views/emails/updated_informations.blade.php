<p>Olá, as informações do Produto {{ $product->variants[0]->title}}, Sku {{ $product->variants[0]->sku}} foram atualizadas. </p>

<p>Atualização:
O Estoque foi atualizado (Quando o fornecedor adiciona mais estoque?)
O Produto não está mais disponível no catálogo do Fornecedor (Quando o fornecedor exclui o produto ou quando o estoque zera?)
O preço do produto foi alterado
O fornecedor acrescentou novas Variantes
O Fornecedor alterou o Nome do Produto
O Fornecedor alterou o CEP de Postagem do Produto (Quando o fornecedor altera o próprio cep?)
</p>
<p>
    Atenciosamente, <br>
    <b>{{env('APP_NAME')}}</b>
</p>