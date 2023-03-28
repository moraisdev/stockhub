<p>Olá {{ $return->supplier_order->supplier->name }}, o recebimento do reembolso do pedido {{ $return->supplier_order->f_display_id }} foi confirmado pelo lojista. À seguir, você deve confirmar a finalização do processo de reembolso em seu painel. Em caso de dúvidas, entre em contato com nosso suporte.</p>

<p>
    Atenciosamente, <br>
    <b>{{env('APP_NAME')}}</b>
</p>
