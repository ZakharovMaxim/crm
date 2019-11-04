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
Route::post('/authenticate', 'UserController@authenticate');
Route::group(['middleware' => ['jwt.verify']], function() {
    // allowed for all
    Route::get('/modules', 'AppController@index');

    //allowed for admin
    Route::group(['middleware' => 'jwt.is_admin'], function () {
        Route::get('/users/roles_info', 'UserController@roles_info');
        Route::patch('/users/{user}/role', 'UserController@set_role');
        Route::patch('/users/{user}/restore', 'UserController@restore');
        Route::post('/users', 'UserController@store');
        Route::delete('/users/{user}', 'UserController@destroy');
        Route::get('/users', 'UserController@index');
        Route::apiResource('plugins', 'PluginController');
        Route::patch('/plugins/{plugin}/settings', 'PluginController@update_settings');
        Route::patch('/plugins/{plugin}/settings/state', 'PluginController@set_state');
    });
    // allowed by modules
    Route::group(['middleware' => 'jwt.module:1'], function () {
        Route::apiResource('shops', 'ShopController');
    });
    // reports start
    Route::group(['middleware' => 'jwt.module:7'], function () {
        Route::get('/orders/index_report', 'OrderController@index_report');
    });
    Route::group(['middleware' => 'jwt.module:8'], function () {
        Route::get('/products/index_report_stocks', 'ProductController@index_report');
    });
    Route::group(['middleware' => 'jwt.module:9'], function () {
        Route::get('/products/index_report_products', 'ProductController@index_report');
    });
    Route::group(['middleware' => 'jwt.module:10'], function () {
        Route::get('/payments/index_report', 'PaymentController@index_report');
    });
    Route::group(['middleware' => 'jwt.module:11'], function () {
        Route::get('/payment-states/index_report', 'PaymentStateController@index_report');
    });
    // reports end
    // -------------catalog start
    Route::group(['middleware' => 'jwt.module:2'], function () {
        Route::group(['middleware' => 'jwt.catalog'], function () {
            Route::get('/folders/info', 'FolderController@info');
            Route::apiResource('folders', 'FolderController');
            Route::get('products/export', 'ProductController@export');
            Route::post('products/import', 'ProductController@import');
            Route::apiResource('products', 'ProductController');
            Route::apiResource('attributes', 'AttributeController');
            Route::apiResource('attribute-values', 'AttributeValueController');
            Route::post('/delete-products-attribute', 'ProductController@delete_attribute');
            Route::post('/add-products-attribute', 'ProductController@add_attribute');
            Route::patch('/products/{product}/price-groups', 'ProductController@price_groups');
            Route::get('/products/{product}/info', 'ProductController@info');
            Route::post('/images', 'ImageController@store');
            Route::post('/images/sort', 'ImageController@sort');
            Route::delete('/images/{image}', 'ImageController@destroy');
        });
        Route::apiResource('trademarks', 'TrademarkController');
        Route::apiResource('price-groups', 'PriceGroupController');
        Route::apiResource('currencies', 'CurrencyController');
    });
    // --------------catalog end

    // ------------------finance start
    Route::group(['middleware' => 'jwt.module:5'], function () {
        Route::apiResource('payment-categories', 'PaymentCategoryController');
        Route::get('/payments/info', 'PaymentController@info');
        Route::resource('payments', 'PaymentController');
        Route::resource('payment-states', 'PaymentStateController');
        Route::apiResource('bills', 'BillController');
    });
    // ------------------finance end

    // ------------------stock start
    Route::group(['middleware' => 'jwt.module:4'], function () {
        Route::group(['middleware' => 'jwt.shop:stock,4'], function () {
            Route::resource('stocks', 'StockController');
            Route::patch('/stocks/{stock}/folders', 'StockController@update_folders');
        });
        Route::group(['middleware' => 'jwt.stock:4'], function () {
            Route::post('/stock-actions/{action}/products', 'StockActionController@add_products');
            Route::delete('/stock-actions/{action}/products/{product}', 'StockActionController@delete_product');
            Route::post('/stock-actions', 'StockActionController@store');
            Route::get('/stock-actions', 'StockActionController@index');
            Route::get('/stock-actions/{action}', 'StockActionController@find');
            Route::patch('/stock-actions/{action}', 'StockActionController@update');
            Route::patch('/stock-actions/{action}/submit', 'StockActionController@submit');
            Route::delete('/stock-actions/{action}', 'StockActionController@destroy');
        });
    });
    // ------------------stock end
    Route::group(['middleware' => 'jwt.module:4,6'], function () {
        Route::get('/stocks/{stock}/products', 'StockController@get_products');
    });
    // ------------------orders start
    Route::group(['middleware' => 'jwt.module:6'], function () {
        Route::group(['middleware' => 'jwt.shop:order,6'], function () {
            Route::get('/orders/{order}/ttn', 'OrderController@create_ttn');
            Route::post('/orders/{order}/ttn', 'OrderController@store_ttn');
            Route::post('/orders/ttn/get', 'OrderController@get_orders_statuses_np');
            Route::get('/orders/warehouses/{code}', 'OrderController@get_warehouses');
            Route::patch('orders/{order}/fields', 'OrderController@update_field');
            Route::post('/orders/{order}/products', 'OrderController@add_products');
            Route::patch('/orders/{order}/products', 'OrderController@update_product');
            Route::patch('/orders/{order}/status', 'OrderController@update_status');
            Route::patch('/orders/{order}/discount', 'OrderController@set_discount');
            Route::patch('/orders/{order}/ttn', 'OrderController@set_ttn');
            Route::delete('/orders/{order}/products/{product}', 'OrderController@delete_product');
            Route::get('orders/info/all', 'OrderController@info');
            Route::get('orders/info/stats', 'OrderController@stats');
            Route::resource('orders', 'OrderController');
        });
    });
    // ------------------orders end
    // ------------------clients start
    Route::group(['middleware' => 'jwt.module:12'], function () {
        Route::apiResource('clients', 'ClientController');
    });
    // ------------------clients end
    // allowed only for yourselves or admin
    
    Route::group(['middleware' => 'jwt.yourself'], function () {
        Route::patch('/users/{user}/password', 'UserController@change_password');
        Route::post('/users/{user}/image', 'UserController@set_image');
        Route::delete('/users/{user}/image', 'UserController@delete_image');
        Route::get('/users/{user}', 'UserController@show');
        Route::patch('/users/{user}', 'UserController@update');
    });
});