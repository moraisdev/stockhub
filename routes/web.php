<?php

use Illuminate\Support\Facades\Route;
//website
Route::namespace('Site')->as('site.')->group(function () {
    //Route::get('/', 'SiteController@index')->name('index');

    Route::get('/', function () {
        return redirect(config('app.site_url'));
    })->name('index');
    // Route::get('download_receipt/{customer_id}/{receipt_id}', 'SiteController@downloadReceipt')->name('download_receipt');
    // Route::post('contact_form', 'SiteController@contact_form')->name('contact_form');
    // Route::post('newsletter', 'SiteController@newsletter')->name('newsletter');


});

Route::get('shop/catalog/index', 'shop\CatalogoController@catolog')->name('catalogindex');
Route::post('shop/products/rate/{product}', 'Shop\ProductsController@rate')->name('shop.products.rate');

Route::namespace('Supplier')->as('supplier.')->prefix('supplier')->group(function () {
    // consulta pagamento plano pix
    Route::any('consultapixger', 'PlansController@pay_plano_consulta')->name('consultapixplano');
    
    
    
    // Login Routes
    Route::get('login', 'LoginController@index')->name('login');
    Route::get('login/register', 'LoginController@register')->name('login.register');
    Route::get('login/forgot_password', 'LoginController@forgotPassword')->name('login.forgot_password');
    Route::get('login/define_new_password/{hash}', 'LoginController@defineNewPassword')->name('login.define_new_password');

    Route::post('login/authenticate', 'LoginController@authenticate')->name('login.authenticate');
    Route::post('login/register', 'LoginController@postRegister')->name('login.post_register');
    Route::post('login/forgot_password', 'LoginController@postForgotPassword')->name('login.forgot_password.post');
    Route::post('login/define_new_password/{hash}', 'LoginController@postDefineNewPassword')->name('login.define_new_password.post');

    //Route::middleware(['auth:supplier', 'supplier.plan'])->group(function(){
    Route::middleware(['auth:supplier'])->group(function () {
        /* DASHBOARD */
        Route::get('/', 'DashboardController@index')->name('dashboard');

        /* PROFILE */
        Route::get('profile', 'ProfileController@index')->name('profile');
        Route::put('profile/update', 'ProfileController@update')->name('profile.update');
        Route::get('profile/toggle_status', 'ProfileController@toggleStatus')->name('profile.toggle_status');

        /* LOGOUT */
        Route::get('logout', 'LoginController@logout')->name('logout');

        /* PARTNERS */
        Route::get('partners', 'PartnersController@index')->name('partners.index');
        Route::get('partners/{shop_id}', 'PartnersController@show')->name('partners.show');

        /* ORDERS */
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        Route::get('orders/sent/search', 'OrdersController@searchSent')->name('orders.sent.search');
        Route::get('orders/print_pending_tags', 'OrdersController@printPendingTags')->name('orders.print_pending_tags');
        Route::post('orders/print_pending_tags_melhor_envio', 'OrdersController@printTagsMelhorEnvio')->name('orders.print_pending_tags_melhor_envio');
        Route::get('orders/update_manual_melhor_envio/{order_id}', 'OrdersController@updateManualMelhorEnvio')->name('orders.update_manual_melhor_envio');
        Route::get('orders/print_pending_content_declaration', 'OrdersController@printPendingContentDeclaration')->name('orders.print_pending_content_declaration');
        Route::get('orders/choose_orders_to_spreadsheet', 'OrdersController@chooseOrdersToSpreadsheet')->name('orders.choose_orders_to_spreadsheet');
        Route::get('orders/{order_id}', 'OrdersController@show')->name('orders.show');
        Route::get('orders/{order_id}/print_tag', 'OrdersController@printTag')->name('orders.print_tag');
        Route::get('orders/{order_id}/print_content_declaration', 'OrdersController@printContentDeclaration')->name('orders.print_content_declaration');
        Route::get('orders/{order_id}/get_json', 'OrdersController@jsonOrder')->name('orders.get_json');
        Route::get('orders/download_receipt/{receipt_id}', 'OrdersController@downloadReceipt')->name('orders.download_receipt');
        Route::post('orders/generate_spreadsheet', 'OrdersController@generateSpreadsheet')->name('orders.generate_spreadsheet');
        Route::post('orders/{order_id}/cancel', 'OrdersController@cancel')->name('orders.cancel');
        Route::post('orders/{order_id}/upload_receipt', 'OrdersController@uploadReceipt')->name('orders.upload_receipt');
        Route::any('orders/{order_id}/update_shipping', 'OrdersController@updateShipping')->name('orders.update_shipping');
        Route::post('orders/{order_id}/revert_shipping', 'OrdersController@revertShipping')->name('orders.revert_shipping');
        Route::post('orders/{order_id}/cancel_shipping', 'OrdersController@cancelShipping')->name('orders.cancel_shipping');
        Route::post('orders/{order_id}/update_returned', 'OrdersController@updateReturned')->name('orders.update_returned');
        Route::post('orders/update_shipping/selected', 'OrdersController@updateShippingSelected')->name('orders.update_shipping.selected');
        Route::post('orders/{order_id}/update_comments', 'OrdersController@updateComments')->name('orders.update_comments');
        Route::post('orders/update_tracking_number_bling', 'OrdersController@updateOrderTrackingNumberBling')->name('orders.update_tracking_number_bling');
        Route::post('orders/update_tracking_number_china_division', 'OrdersController@updateOrderTrackingNumberChinaDivision')->name('orders.update_tracking_number_china_division');
        Route::post('orders/total_express/send/{order_id}', 'OrdersController@sendOrderToTotalExpress')->name('orders.total_express.send');
        Route::any('orders/{order_id}/update_shippinge', 'OrdersController@updateShippingE')->name('orders.update_shipping_e');
       
        /* DESTROY ORDER ADMIN ONLY */
        Route::delete('orders/destroy/{order_id}', 'OrdersController@destroy')->name('orders.destroy');

		/* PRODUCTS */
        Route::get('produtos/import/csv_instructions', 'ProductsController@csvInstructions')->name('products.import.csv_instructions');
        Route::get('produtos/import/download_csv', 'ProductsController@downloadCsvModel')->name('products.import.download_csv_model');
        Route::post('produtos/import/excel', 'ProductsController@importCsv')->name('products.import.csv');
        Route::get('products/{product_id}/aliexpress_link_product/{ae_product_id}', 'ProductsController@aliexpressLinkProduct')->name('products.aliexpress.link_product');
        Route::put('products/{product_id}/update_variants_skus', 'ProductsController@updateVariantsSkus')->name('products.aliexpress.update_variants_skus');
        Route::put('products/{product_id}/variants/{variant_id}/publish', 'ProductsController@publishVariant')->name('products.variants.publish');
        Route::put('products/{product_id}/variants/{variant_id}/unpublish', 'ProductsController@unpublishVariant')->name('products.variants.unpublish');
        Route::delete('products/delete_image/{image}', 'ProductsController@deleteImage')->name('products.delete_image');
        
        Route::get('products/import/bling', 'ProductsController@importProductsBling')->name('products.import.bling');
        
        Route::get('products/import/bling_json_a/{product_id}', 'ProductsController@importProdutoBlingJson')->name('products.import.bling.json_a');
        Route::get('products/import/bling_json', 'ProductsController@importProductsBlingJson')->name('products.import.bling.json'); 
        Route::post('products/import/bling_json/store', 'ProductsController@storeBlingProductVariant')->name('products.import.bling.json.store');
        Route::post('products/massive-edit', 'ProductsController@massiveEdit')->name('products.massive.edit');
        Route::post('products/massive-update', 'ProductsController@massiveUpdate')->name('products.massive.update');
        
        Route::get('products/import/bling_json', 'ProductsController@importProductsBlingJson')->name('products.import.bling.json');
        Route::get('products/import/img_bling_json', 'ProductsController@importProductsBlingImagem')->name('products.import.img.bling.json');

        Route::get('products/edit/public/{product_id}', 'ProductsController@editpublic')->name('products.edit_public');
        Route::get('products/tabelas', 'ProductsController@tabelas')->name('products.tabelas');
        Route::delete('products/delete/{product_id}', 'ProductsController@destroy')->name('products.destroy');
        Route::post('produtos/save', 'ProductsController@register')->name('products.register');


        Route::resource('products', 'ProductsController');
        
        Route::get('collective', 'CollectiveController@index')->name('collective.index');
        Route::get('collective/tabelas', 'CollectiveController@tabelas')->name('collective.tabelas');
        Route::post('collective/update/{collective_id}', 'CollectiveController@update')->name('collective.update');
        Route::get('collective/edit/{collective_id}', 'CollectiveController@edit')->name('collective.edit');
        Route::get('download/invoice/{id}', 'FileController@downloadInvoice')->name('download.invoice');
        Route::get('download/packing-list/{id}', 'FileController@downloadPackingList')->name('download.packingList');
        Route::get('download/pdf-import-collective/{id}', 'FileController@downloadPdfImportCollective')->name('download.pdfImportCollective');

        /* RETURNS */
        Route::get('returns', 'ReturnsController@index')->name('returns.index');
        Route::get('returns/{id}', 'ReturnsController@show')->name('returns.show');
        Route::get('returns/{id}/confirm', 'ReturnsController@confirm')->name('returns.confirm');
        Route::post('returns/{id}/new_message', 'ReturnsController@newMessage')->name('returns.new_message');

        /* SETTINGS */
        Route::get('settings', 'SettingsController@index')->name('settings.index');
        Route::put('settings/shopify', 'SettingsController@updateMPCredentials')->name('settings.update_mp_credentials');
        Route::put('settings/woocommerce', 'SettingsController@updateMPCredentials')->name('settings.update_mp_credentials');
        Route::put('settings/correios', 'SettingsController@updateCorreiosSettings')->name('settings.update_correios');
        Route::put('settings/bling', 'SettingsController@updateBlingSettings')->name('settings.update_bling');
        Route::put('settings/china_division', 'SettingsController@updateChinaDivisionSettings')->name('settings.update_china_division');
        Route::put('settings/total_express', 'SettingsController@updateTotalExpressSettings')->name('settings.update_total_express');
        Route::put('settings/melhor_envio', 'SettingsController@updateMelhorEnvioSettings')->name('settings.update_melhor_envio');
        Route::put('settings/no_shipping', 'SettingsController@updateNoShippingSettings')->name('settings.update_no_shipping');
        Route::put('settings/shipping_fee', 'SettingsController@updateShippingFee')->name('settings.update_shipping_fee');
        Route::get('settings/discounts', 'SettingsController@discounts')->name('settings.discounts');
        Route::post('settings/discounts/store', 'SettingsController@store_discount')->name('settings.discounts.store');
        Route::get('settings/discounts/delete/{id}', 'SettingsController@delete_discount')->name('settings.discounts.delete');
        Route::put('settings/correios_contract', 'SettingsController@updateCorreiosContract')->name('settings.correios_contract');
        Route::post('settings/update-etiqueta_ml', 'SettingsController@updateEtiquetaML')->name('settings.update_etiqueta_ml');
        Route::post('settings/update-autocom', 'SettingsController@updateAutocom')->name('settings.update_autocom');

        /* PLANS */
        Route::get('plans', 'PlansController@index')->name('plans.index');
        Route::get('plans/select/{plan_id}', 'PlansController@selectedPlan')->name('plans.selected');
        Route::post('plans/store', 'PlansController@store')->name('plans.store');
        Route::get('plans/cancel', 'PlansController@cancel')->name('plans.cancel');
        Route::post('plans/store/cancel', 'PlansController@storeCancel')->name('plans.store.cancel');
        Route::get('settings/melhor-envio', 'DashboardController@redirectMelhorEnvio')->name('settings.melhor_envio');
        Route::get('settings/melhor-envio/remove', 'SettingsController@removeMelhorEnvioSettings')->name('settings.melhor_envio.remove');
        Route::get('plans/invoicesupplier', 'PlansController@invoice')->name('plans.invoice');
        Route::get('payment/detail/{id}', 'PlansController@paymentdetail')->name('plans.detail');
        Route::get('payment/pay/{id}', 'PlansController@paymentpay')->name('plans.pay');
        Route::get('invoices/pay/{id}/{payment_method}', 'PlansController@planspay')->name('plans.planspay');
        Route::get('plans', 'PlansController@index')->name('plans.indexplan');
        /* TUTORIALS */
        Route::get('tutorials', 'TutorialsController@index')->name('tutorials.index');
    });
});

Route::namespace('Shop')->as('shop.')->prefix('shop')->group(function () {
    // consultapagmento pix gerencianet
    Route::any('consultapixger', 'OrdersController@pay_group_consulta')->name('consultapixger');
    Route::any('consultapixpay', 'PlansController@pay_plano_consulta')->name('consultapixplano');
	
    
    // catalogo pagina login
    Route::get('login/catalogo', 'LoginController@catolog')->name('catalogo');
    Route::get('login/catalogo/{id}', 'LoginController@produtodetalhe')->name('catalogodetalhe');
    Route::get('login/categoria/{id}', 'LoginController@produtocategoria')->name('categoria');


    // Login Routes
    
	Route::get('login', 'LoginController@index')->name('login');
    Route::get('login/register', 'LoginController@register')->name('login.register');
    Route::get('login/forgot_password', 'LoginController@forgot_password')->name('login.forgot_password');
    Route::get('login/forgot_password', 'LoginController@forgotPassword')->name('login.forgot_password');
    Route::get('login/define_new_password/{hash}', 'LoginController@defineNewPassword')->name('login.define_new_password');

    Route::post('login/authenticate', 'LoginController@authenticate')->name('login.authenticate');
    Route::post('login/register', 'LoginController@postRegister')->name('login.post_register');
    Route::post('login/register/json', 'LoginController@postRegisterJson')->name('login.post_register.json');
    Route::post('login/register/plan/json', 'LoginController@postRegisterShopPlanJson')->name('login.post_register.plan.json');
    Route::post('login/register/card/json', 'LoginController@postRegisterShopCardJson')->name('login.post_register.card.json');
    Route::post('login/forgot_password', 'LoginController@postForgotPassword')->name('login.forgot_password.post');
    Route::post('login/define_new_password/{hash}', 'LoginController@postDefineNewPassword')->name('login.define_new_password.post');

    /* CUPONS */
    Route::get('coupon/subscription', 'CouponInternalSubscriptionController@isValid')->name('coupon.subscription.isvalid');

    Route::middleware(['auth:shop'])->group(function () {
        /* DASHBOARD */
        Route::get('/', 'DashboardController@index')->name('dashboard');

        /* LOGOUT */
        Route::get('logout', 'LoginController@logout')->name('logout');

        /* PROFILE */
        Route::get('profile', 'ProfileController@index')->name('profile');
        Route::put('profile/update', 'ProfileController@update')->name('profile.update');
        /* PROFILE  UPDATE BLING*/
        Route::put('profile/updatebling', 'ProfileController@updatebling')->name('profile.updatebling');
        Route::get('profile/business', 'BusinessController@index')->name('profile.business');
        Route::get('profile/business/update', 'BusinessController@update')->name('profile.business.update');

        /* PARTNERS */
        Route::get('partners', 'PartnersController@index')->name('partners.index');
        Route::get('partners/{supplier}', 'PartnersController@show')->name('partners.show');
        Route::get('partners/{supplier_slug}/{private_hash}', 'PartnersController@products')->name('partners.products');

        /* ORDERS */
        
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        Route::get('orders/pending/search', 'OrdersController@search')->name('orders.pending.search');
        Route::get('orders/import', 'OrdersController@import')->name('orders.import');
        Route::get('orders/importWoo', 'OrdersController@importWoo')->name('orders.importWoo');
        Route::get('orders/import-cartx', 'OrdersController@importCartx')->name('orders.import_cartx');
        Route::get('orders/import-yampi', 'OrdersController@importYampi')->name('orders.import_yampi');

        Route::post('orders/create', 'OrdersController@createOrder')->name('orders.create');
        
        Route::get('orders/history', 'OrdersController@history')->name('orders.history');
        Route::get('orders/sent', 'OrdersController@sent')->name('orders.sent');
        Route::get('orders/sent/search', 'OrdersController@sentSearch')->name('orders.sent.search');
        Route::get('orders/completed', 'OrdersController@completed')->name('orders.completed');
        Route::get('orders/returned', 'OrdersController@returned')->name('orders.returned');
        Route::post('orders/solve_returned/{order_id}', 'OrdersController@solveReturned')->name('orders.solve_returned');
        Route::get('orders/check_resend/{order_id}', 'OrdersController@checkResendOrder')->name('orders.check_resend');
    
      /* importar pedido bling  */
        Route::get('orders/import-pedido-bling', 'OrdersController@importBling')->name('orders.import_pedido_bling');
        Route::get('orders/import_order_ml', 'OrdersController@importOrderMercadolivre')->name('orders.mercadolivre'); 
        /* bling rastreamento */
        Route::get('orders/update_tracking_number_bling/{order_id}', 'OrdersController@updateOrderTrackingNumberBling')->name('orders.update_tracking_number_bling');
         /* rotta img do produto devolvido */
         Route::post('orders/returns/messages/img/{order_id}', 'ReturnsController@returnNewMessageimg')->name('orders.return.new_essage_img');
       
  
    
        Route::post('orders/generate_resend_invoice', 'OrdersController@generateResendInvoice')->name('orders.generate_resend_invoice');
        Route::get('orders/paid', 'OrdersController@paid')->name('orders.paid');
        Route::any('orders/prepare_payment', 'OrdersController@prepare_payment')->name('orders.prepare_payment');
        Route::get('orders/invoices/pending', 'OrdersController@pending_groups')->name('orders.pending_groups');
        Route::get('orders/invoices/paid', 'OrdersController@paid_groups')->name('orders.paid_groups');
        Route::get('orders/invoices/pay/{group_id}/{payment_method}', 'OrdersController@pay_group')->name('orders.groups.pay');
        Route::get('orders/invoices/{group_id}', 'OrdersController@group_detail')->name('orders.group_detail');



        Route::post('orders/invoices/apply_discount/{group_id}', 'OrdersController@applyDiscount')->name('orders.groups.apply_discount');
        Route::delete('orders/invoices/delete/{group_id}', 'OrdersController@deleteGroup')->name('orders.groups.delete');
        Route::delete('orders/invoices/delete_order/{group_id}/{order_id}', 'OrdersController@deleteOrderInGroup')->name('orders.groups.order.delete');
        Route::get('orders/cancel/{order_id}', 'OrdersController@cancel')->name('orders.cancel');
        Route::get('orders/{order_id}', 'OrdersController@show')->name('orders.show');
        Route::get('orders/{order_id}/get_json', 'OrdersController@jsonOrder')->name('orders.get_json');
        Route::get('orders/download_receipt/{receipt_id}', 'OrdersController@downloadReceipt')->name('orders.download_receipt');
        Route::post('orders/{order_id}/add_item', 'OrdersController@addItem')->name('orders.add_item');
        Route::put('orders/items/{item_id}/update_item', 'OrdersController@updateItem')->name('orders.update_item');
        Route::put('orders/{order_id}/update_customer', 'OrdersController@updateCustomer')->name('orders.update_customer');
        Route::delete('orders/items/{item_id}/remove_item', 'OrdersController@removeItem')->name('orders.remove_item');
        Route::post('orders/{order_id}/upload_receipt', 'OrdersController@uploadReceipt')->name('orders.upload_receipt');


        Route::get('orders/returns/{order_id}', 'ReturnsController@ask_return')->name('orders.ask_return');
        Route::post('orders/returns/messages/new', 'ReturnsController@returnNewMessage')->name('orders.return.new_essage');
      
        

    
      
      
        /* PAYMENTS */
        //Route::get('payments/group/{id}', 'PaymentsController@group')->name('shop.orders.group');

        /* PRODUCTS */
        Route::get('products', 'ProductsController@index')->name('products.index');
        Route::get('products/details/{hash}', 'ProductsController@details')->name('products.details');
        Route::get('products/history', 'ProductsController@history')->name('products.history');
        Route::get('products/image/{productImage}', 'ProductsController@downloadImage')->name('products.download');
        Route::get('products/export/{product}', 'ProductsController@export')->name('products.export');
        Route::get('products/export-json', 'ProductsController@exportShopifyJson')->name('products.export_shopify_json');
        Route::post('products/export-images-json', 'ProductsController@exportImagesProductShopifyJson')->name('products.export_images_shopify_json');
        Route::get('products/exportWoocommerce/{product}', 'ProductsController@exportWoocommerce')->name('products.exportWoocommerce');
        Route::get('products/exportWoo-json', 'ProductsController@exportWoocommerceJson')->name('products.export_woocommerce_json');
        Route::post('products/exportWoo-images-json', 'ProductsController@exportImagesProductWoocommerceJson')->name('products.export_images_woocommerce_json');
        Route::get('products/export-cartx/{product}', 'ProductsController@exportCartx')->name('products.export-cartx'); //rota para exportar para o cartx
        Route::any('products/link', 'ProductsController@link')->name('products.link_private');         
        Route::get('products/export-yampi', 'ProductsController@exportYampiJson')->name('yampi.export_product');
        Route::post('products/export-yampi-images/json', 'ProductsController@exportImagesProductYampiJson')->name('yampi.export_images');
        Route::get('products/{product}', 'ProductsController@show')->name('products.show');

        Route::get('products/export-ml/{idproduct}', 'ProductsController@exportMercadolivre')->name('products.export-ml'); //rota para exportar para o mercadoliver
        Route::get('products/exportyampi/{idproduct}', 'ProductsController@exportYampiNovo')->name('yampi.exp_product');
        Route::get('products/exportybling/{idproduct}', 'ProductsController@exportBling')->name('bling.exp_product');

        /* CATALOG */
        Route::get('catalog', 'CatalogController@index')->name('catalog.index');
        Route::get('catalog/add_product/{product_id}', 'CatalogController@addProduct')->name('catalog.add_product');

        /* SETTINGS */
        Route::get('settings', 'SettingsController@index')->name('settings.index');
        Route::put('settings/shopify', 'SettingsController@updateShopifyApp')->name('settings.update_shopify_app');
        Route::put('settings/woocommerce', 'SettingsController@updateWoocommerceApp')->name('settings.update_woocommerce_app');
        Route::put('settings/cartx', 'SettingsController@updateCartxApp')->name('settings.update_cartx_app');
        Route::put('settings/yampi', 'SettingsController@updateYampiApp')->name('settings.update_yampi_app');
        Route::get('settings/remove-free-card', 'SettingsController@removeFreeCard')->name('settings.index.remove_free_card');
   
        Route::put('settings/mercadolivre', 'SettingsController@updateMercadolivreApp')->name('settings.update_mercadolivre_app');
        Route::put('settings/shoppe', 'SettingsController@updateShopeeApp')->name('settings.update_shoppe_app');
       


        /* PLANS */
        Route::get('plans', 'PlansController@index')->name('plans.index');
        Route::get('plans/select/{plan_id}', 'PlansController@selectedPlan')->name('plans.selected');
        Route::post('plans/store', 'PlansController@store')->name('plans.store');
        Route::get('plans/cancel', 'PlansController@cancel')->name('plans.cancel');
        Route::post('plans/store/cancel', 'PlansController@storeCancel')->name('plans.store.cancel');
        Route::get('invoiceshop', 'PlansController@invoice')->name('plans.invoice');
        Route::get('payment/detail/{id}', 'PlansController@paymentdetail')->name('plans.detail');
        Route::get('payment/pay/{id}', 'PlansController@paymentpay')->name('plans.pay');
        Route::get('invoices/pay/{id}/{payment_method}', 'PlansController@planspay')->name('plans.planspay');
      
        /* TUTORIALS */
        Route::get('tutorials', 'TutorialsController@index')->name('tutorials.index');

        /* RADAR */
        Route::get('radar', 'RadarController@index')->name('radar.index');
        Route::get('radar/buy', 'RadarController@buy')->name('radar.buy');
        Route::get('radar/activate', 'RadarController@activate')->name('radar.activate');

        /* CONTAINTER PRIVADO */
        Route::get('private', 'ContainerPrivateController@index')->name('private.index');

        /* CONTAINTER COLETIVO */
        Route::get('collective', 'CollectiveController@index')->name('collective.index');
        Route::get('collective/new', 'CollectiveController@new')->name('collective.new');
        Route::post('collective/store', 'CollectiveController@store')->name('collective.store');

        /* SIMULADOR DE FRETE */
        Route::get('freight-simulator', 'FreightSimulatorController@index')->name('freight-simulator.index');
        Route::get('freight-simulator/simulate', 'MelhorEnvioController@simulate')->name('freight-simulator.simulate');

        Route::get('returns', 'ReturnsController@index')->name('returns.index');
        Route::get('returns/confirm/{id}', 'ReturnsController@confirm')->name('returns.confirm');
        Route::get('returns/cancel/{id}', 'ReturnsController@cancel')->name('returns.cancel');
        
        
          /* BLING SHOP */
        Route::get('export-bling-json', 'BlingController@exportBlingJson')->name('export_bling_json');
    });
});

// Login Routes
Route::namespace('Admin')->as('admin.')->prefix('admin')->group(function () {
    Route::get('login', 'LoginController@index')->name('login');
    Route::post('login/authenticate', 'LoginController@authenticate')->name('login.authenticate');
    Route::get('test', 'DashboardController@test');

    Route::middleware('auth:admin')->group(function () {
        /* DASHBOARD */
        Route::get('/', 'DashboardController@index')->name('dashboard');

        /* site institucional */
        Route::get('/institucional', 'SiteIntitucionalController@index')->name('institucional');
        Route::put('/institucional/update/{id}', 'SiteIntitucionalController@update')->name('institucional.update');

        /* BANNERS */
        Route::resource('banners', 'ShopBannersController');

        /* LINKS AFILIADOS */
        Route::resource('affiliate-link', 'AffiliateLinkController');

        /* LOGOUT */
        Route::get('logout', 'LoginController@logout')->name('logout');

        /* SUPPLIERS */
        Route::get('suppliers', 'SuppliersController@index')->name('suppliers.index');
        Route::get('suppliers/{supplier}', 'SuppliersController@show')->name('suppliers.show');
        Route::post('suppliers/{supplier}','SuppliersController@save')->name('suppliers.save');
        Route::get('suppliers/{supplier}/login', 'SuppliersController@login')->name('suppliers.login');
        Route::get('suppliers/{supplier}/toggle_status', 'SuppliersController@toggleStatus')->name('suppliers.toggle_status');
        Route::get('suppliers/{supplier}/toggle_login', 'SuppliersController@toggleLogin')->name('suppliers.toggle_login');
        Route::get('suppliers/{supplier}/toggle_shipment_address', 'SuppliersController@toggleShipmentAddress')->name('suppliers.toggle_shipment_address');
        Route::get('suppliers/send_to_safe2pay/{supplier}', 'SuppliersController@sendToSafe2Pay')->name('suppliers.send_to_safe2pay');
        Route::post('suppliers/{supplier}', 'SuppliersController@delete')->name('suppliers.delete');
       
        /* SHOPS */
        Route::get('shops/login/{{token}}', 'ShopsController@confirmEmail')->name('shop.confirmar_email');
        Route::get('shops', 'ShopsController@index')->name('shops.index');
        Route::get('shops/search', 'ShopsController@search')->name('shops.search');
        Route::get('shops/{shop}', 'ShopsController@show')->name('shops.show');
        Route::get('shops/{shop}/login', 'ShopsController@login')->name('shops.login');
        Route::get('shops/{shop}/toggle_status', 'ShopsController@toggleStatus')->name('shops.toggle_status');
        Route::get('shops/{shop}/toggle_login', 'ShopsController@toggleLogin')->name('shops.toggle_login');
        Route::get('shops/{shop}/more_days_free', 'ShopsController@moreDaysFree')->name('shops.more_days_free');
        Route::delete('shops/{shop}', 'ShopsController@delete')->name('shops.delete');
        Route::delete('shops/{shop}/delete-card', 'ShopsController@deleteCard')->name('shops.delete.card');


        Route::get('products/import/{supplier_id}', 'ProductsController@import')->name('products.import');
        Route::get('products/update_descriptions/{supplier_id}', 'ProductsController@updateDescriptions')->name('products.update_descriptions');
        Route::post('products/update_descriptions/{supplier_id}', 'ProductsController@postUpdateDescriptions')->name('products.update_descriptions.post');
        Route::post('products/import/{supplier_id}', 'ProductsController@postImport')->name('products.import.post');
        Route::post('products/import_csv/{supplier_id}', 'ProductsController@importCSV')->name('products.import_csv');

        Route::get('orders/shops', 'OrdersController@shops')->name('orders.shops');
        Route::get('orders/shops/{order}', 'OrdersController@showShopOrder')->name('orders.shops.show');
        Route::get('orders/suppliers', 'OrdersController@suppliers')->name('orders.suppliers');
        Route::get('orders/suppliers/{supplier_order}', 'OrdersController@showSupplierOrder')->name('orders.suppliers.show');
        Route::get('orders/suppliers/{supplier_order}/print_tag', 'OrdersController@printTag')->name('orders.suppliers.print_tag');
        Route::get('orders/suppliers/{supplier_order}/print_content_declaration', 'OrdersController@printContentDeclaration')->name('orders.suppliers.print_content_declaration');

        /* CATEGORIES */
        Route::resource('categories', 'CategoriesController');

        /* SETTINGS */
        Route::get('settings', 'SettingsController@index')->name('settings.index');
        Route::put('settings/credentials', 'SettingsController@updateCredentials')->name('settings.update_credentials');
        Route::post('settings/update-melhor-envio', 'SettingsController@updateMelhorEnvioSettings')->name('settings.update_melhor_envio');
        Route::put('settings/update-settings', 'SettingsController@updateSettings')->name('settings.update_settings');
        Route::put('settings/update-etiqueta_ml', 'SettingsController@updateEtiquetaML')->name('settings.update_etiqueta_ml');
      


         /* Tutoriais */
         Route::get('tutoriais', 'TutorialController@index')->name('tutorial.index');
         Route::get('tutoriais/update/{id}', 'TutorialController@edit')->name('tutorial.edit');
         Route::put('tutoriais/update/{id}', 'TutorialController@update')->name('tutorial.update');
    
         /* Email Templates */

         Route::get('email', 'EmailTemplateController@index')->name('emailtemplate.index');
         Route::get('email/update/{id}', 'EmailTemplateController@edit')->name('emailtemplate.edit');
         Route::put('email/update/{id}', 'EmailTemplateController@update')->name('emailtemplate.update');


         /* Planos Assinaturas lojista  */
         Route::get('planos', 'ShopplanoController@index')->name('planos.index');
         Route::get('planos/update/{id}', 'ShopplanoController@edit')->name('planos.edit');
         Route::delete('planos/update/{id}', 'ShopplanoController@index')->name('planos.delete');
         Route::put('planos/update/{id}', 'ShopplanoController@update')->name('planos.update');
         
         /* supllier  */
         Route::post('suppliers/{supplier}','SuppliersController@save')->name('suppliers.salve');
         Route::delete('suppliers/{supplier}', 'SuppliersController@delete')->name('suppliers.delete');
         
         /* Planos Assinaturas fornecedor  */
         Route::get('planos/updatef/{id}', 'ShopplanoController@editf')->name('planosf.edit');
         Route::delete('planos/updatef/{id}', 'ShopplanoController@indexf')->name('planosf.delete');
         Route::put('planos/updatef/{id}', 'ShopplanoController@updatef')->name('planosf.update');

         /* Planos pagamentos  */
         Route::get('payment/payconfig', 'ShopplanoController@payconfig')->name('plans.payconfig');
         Route::put('payment/update/payconfig', 'ShopplanoController@payconfigup')->name('plans.payconfig_update');
         Route::get('plans/paid', 'ShopplanoController@paid')->name('plans.paid');
        
         
    });
});
