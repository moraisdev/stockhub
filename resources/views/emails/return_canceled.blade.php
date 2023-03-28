<p>Olá {{ $return->supplier_order->supplier->name }}, o reembolso do pedido {{ $return->supplier_order->f_display_id }} foi cancelado pelo lojista. Em caso de dúvidas, entre em contato com nosso suporte.</p>

<p>
    Atenciosamente, <br>
    <b>{{env('APP_NAME')}}</b>
</p>
