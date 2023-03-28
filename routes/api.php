<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('mercado_pago/payment/{hash}', 'Api\MercadoPagoController@payment_webhook')->name('api.mercado_pago.webhooke');
Route::get('cities', 'Api\CitiesController@getCitiesByState')->name('api.cities');
Route::get('shopify/products/suppliers/address', 'Api\SuppliersAddressController@getAddressByProducts')->name('api.shopify.products.suppliers.address');
Route::get('woocommerce/products/suppliers/address', 'Api\SuppliersAddressController@getAddressByProducts')->name('api.woocommerce.products.suppliers.address');
Route::get('valida_img_bling', 'Api\BlingController@valida_img')->name('api.suppliers.valida_img');
Route::get('valida_planos', 'Api\BlingController@planofornecedor')->name('api.valida_planos');
Route::any('safe2pay/webhooks/transaction', 'Api\Safe2payController@transaction')->name('api.safe2pay.transaction');

Route::get('correios/simulate', 'Api\CorreiosController@simulate')->name('api.correios.simulate');

Route::get('simulate/get-zipcode', 'Api\CitiesController@getZipcode')->name('api.melhor_envio.get_zipcode');

Route::any('shopify/webhooks/orders/paid', 'Api\ShopifyController@importOrder');
Route::any('shopify/webhooks/orders/cancelled', 'Api\ShopifyController@cancelOrder');
Route::any('woocommerce/webhooks/orders/paid', 'Api\WoocommerceController@importOrder');
Route::any('woocommerce/webhooks/orders/cancelled', 'Api\WoocommerceController@cancelOrder');
// Route::get('shopify/test-webhook', 'Api\ShopifyController@testeImportWebHook');

Route::any('bling/update-stock', 'Api\BlingController@updateStock');

Route::any('gerencianet/webhooks/orders/paid', 'Api\GerencianetController@webhookboleto');
//Rotas Lojista

Route::group(['prefix'=>'shop','as'=>'api.shop.', 'namespace' => '\App\Http\Controllers\Shop'], function(){


    Route::group(['middleware' => ['auth:shop', 'shop.plan']], function($router){           


        /*SETTINGS*/

        Route::put('settings/yampi', 'SettingsController@updateYampiApp')->name('settings.update_yampi_app');


        /* Yampi */

        //testes

        Route::get('yampi/get_paid_orders', 'Api\YampiController@getPaidOrders')->name('yampi.get_paid_orders');

        Route::get('yampi/get_brands', 'Api\YampiController@getBrands')->name('yampi.get_brands');

        //Import

        Route::get('yampi/import_orders', 'Api\YampiController@importOrders')->name('yampi.import_orders');


    });

});
