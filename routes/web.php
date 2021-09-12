<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (app()->isLocal()) {
    Route::any('test/{method}', 'TestController')->name('test');
    Route::get('fake-payment', 'FakePaymentController@paymentForm')
        ->name('fake-payment.payment');
    Route::post('fake-payment', 'FakePaymentController@payment');
    Route::post('fake-payment/landing', 'FakePaymentController@landing')
        ->name('fake-payment.landing');
    Route::post('fake-payment/request', 'FakePaymentController@request')
        ->name('fake-payment.request');
    Route::post('fake-payment/verify', 'FakePaymentController@verify')
        ->name('fake-payment.verify');
    Route::get('ehsan', function(){
        $files = [];
        foreach (['index', 'features', 'prices', 'why-choose'] as $file) {
            $content = file_get_contents(base_path('resources/views/site/' . $file . '.blade.php'));
            preg_match_all('/url\\(\\/site-asset\\/(.+\.\\w+)\\)/', $content, $matches);
            foreach ($matches[1] as $match){
                $files[] = 'https://www.mightycall.com/wp-content/themes/newmightycall/' . $match;
            }
        }
        foreach ($files as $file){
            $ch = curl_init($file);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);

        }
    });
}


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/about-us', 'HomeController@aboutUs')->name('about-us');
Route::get('/contact-us', 'HomeController@contactUs')->name('contact-us');
Route::get('/faq', 'HomeController@faq')->name('faq');
Route::get('/privacy-policy', 'HomeController@privacyPolicy')->name('privacy-policy');
Route::get('/terms-and-services', 'HomeController@termsAndServices')->name('terms-and-services');

Route::prefix('dashboard')
    ->namespace('Dashboard')
    ->name('dashboard.')
    ->middleware(['auth', 'account.status'])
    ->group(function () {
        Route::middleware(['lock-screen'])
            ->group(function () {
                Route::prefix('client')
                    ->namespace('Client')
                    ->name('client.')
                    ->middleware(['role.check:' . \App\Role::ROLE_CLIENT])
                    ->group(function () {
                        Route::post('support/{ticket}/create-entry', 'SupportController@createEntry')
                            ->name('support.create-entry');
                        Route::resource('support', 'SupportController', ['except' => ['destroy', 'edit', 'update']])
                            ->parameter('support', 'ticket');


                        Route::get('order', 'OrderController@index')->name('order.index');
                        Route::get('order/{order}/invoice', 'OrderController@invoiceIndex')
                            ->name('order.invoice-index');
                        Route::get('order/{order}', 'OrderController@show')->name('order.show');


                        Route::get('invoice', 'InvoiceController@index')->name('invoice.index');
                        Route::post('invoice/{invoice}/pay', 'InvoiceController@pay')->name('invoice.pay');
                        Route::get('invoice/{invoice}', 'InvoiceController@show')->name('invoice.show');

                        Route::get('payment', 'PaymentController@index')->name('payment.index');

                        Route::get('wallet/charge', 'WalletController@chargeForm')->name('wallet.charge');
                        Route::post('wallet/charge', 'WalletController@charge');
                        Route::get('wallet', 'WalletController@index')->name('wallet');
                    });

                Route::prefix('admin')
                    ->namespace('Admin')
                    ->name('admin.')
                    ->middleware(['role.check:' . \App\Role::ROLE_ADMIN])
                    ->group(function () {

                        Route::prefix('catalog')
                            ->namespace('Catalog')
                            ->name('catalog.')
                            ->group(function () {
                                Route::resource('product-category', 'ProductCategoryController', ['except' => ['show', 'destroy']]);
                                Route::resource('product-type', 'ProductTypeController', ['except' => ['show', 'destroy']]);
                                Route::resource('supplier', 'SupplierController', ['except' => ['destroy', 'show']]);
                            });

                        Route::prefix('crm')
                            ->namespace('CRM')
                            ->name('crm.')
                            ->group(function () {
                                Route::post('client/{client}/login-as', 'ClientController@loginAs')->name('client.login-as');
                                Route::resource('client', 'ClientController', ['except' => ['destroy']]);
                                Route::resource('ticket', 'TicketController', ['except' => ['destroy']]);
                                Route::resource('ticket-category', 'TicketCategoryController', ['except' => ['destroy', 'show']]);
                            });

                        Route::prefix('cms')
                            ->namespace('CMS')
                            ->name('cms.')
                            ->group(function () {
                                Route::resource('custom-url', 'CustomUrlController', ['except' => ['show']]);

                                Route::get('gallery', 'MediaController@gallery')
                                    ->name('gallery.index');

                                Route::get('gallery/upload-image', 'MediaController@showUploadImageForm')
                                    ->name('gallery.upload-image');
                                Route::post('gallery/upload-image', 'MediaController@uploadImage');
                                Route::delete('gallery/{image}/delete', 'MediaController@deleteImage')
                                    ->name('gallery.delete-image');

                                Route::resource('post', 'PostController', ['except' => ['show', 'destroy']]);
                                Route::resource('post-category', 'PostCategoryController', ['except' => ['show', 'destroy']]);
                            });

                        Route::prefix('sales')
                            ->namespace('Sales')
                            ->name('sales.')
                            ->group(function () {
                                Route::get('payment', 'PaymentController@index')->name('payment.index');
                                Route::get('payment/{payment}', 'PaymentController@show')->name('payment.show');

                                Route::get('tax-group/{tax_group}/tax-rule', 'TaxRuleController@indexProxy')->name('tax-rule.index');
                                Route::get('tax-group/{tax_group}/tax-rule/create', 'TaxRuleController@createProxy')->name('tax-rule.create');
                                Route::resource('tax-group/{tax_group}/tax-rule', 'TaxRuleController', ['except' => ['index', 'create', 'show']]);

                                Route::delete('product-type-tax-group/{product_type_tax_group}/destroy', 'TaxGroupController@productTypeTaxGroupDestroy')
                                    ->name('product-type-tax-group.destroy');
                                Route::get('tax-group/{tax_group}/product-type', 'TaxGroupController@productTypeIndex')
                                    ->name('tax-group.product-type.index');
                                Route::resource('tax-group', 'TaxGroupController', ['except' => ['destroy', 'show']]);
                            });

                        Route::prefix('system')
                            ->namespace('System')
                            ->name('system.')
                            ->group(function () {
                                Route::get('box-monitor', 'BoxMonitorController@index')->name('box-monitor.index');
                            });
                    });


                Route::get('/', 'PanelController@index')
                    ->name('panel');
                Route::get('profile', 'PanelController@showProfileForm')
                    ->name('profile');
                Route::post('profile', 'PanelController@profile');
                Route::post('lock-screen', 'PanelController@lockScreen')->name('lock-screen');
                Route::get('change-password', 'PanelController@showChangePasswordForm')
                    ->name('change-password');
                Route::post('change-password', 'PanelController@changePassword');
            });

        Route::get('locked-screen', 'PanelController@lockedScreenForm')->name('locked-screen');
        Route::post('locked-screen', 'PanelController@unlockScreen')->middleware('recaptcha');

    });
Route::get('checkout', 'CheckoutController')
    ->name('checkout')
    ->middleware('auth');

Route::get('hosted-pbx/{cart_item?}', 'HostedPBXSessionController@configureBox')->name('hosted-pbx-session');
Route::post('hosted-pbx/{cart_item?}', 'HostedPBXSessionController@addToCart');

Route::get('cart', 'CartController@showCart')->name('cart');
Route::post('cart/{cart_item}', 'CartController@removeItem')->name('remove-cart-item');
Route::post('cart-tax-region', 'CartController@taxRegion')->name('cart-tax-region');
Route::post('redeem-voucher', 'CartController@redeemVoucher')->name('redeem-voucher');

Route::prefix('knowledge-base')
    ->name('knowledge-base.')
    ->group(function () {
        Route::get('/', 'KnowledgeBaseController@index')->name('index');
        Route::get('/category/{category}', 'KnowledgeBaseController@categoryIndex')->name('category');
        Route::get('/post/{post}', 'KnowledgeBaseController@post')->name('post');
    });

Route::prefix('payment')
    ->name('payment.')
    ->group(function () {
        Route::get('forward/{payment}', 'PaymentController@forward')->name('forward');
        Route::match(['get', 'post'], 'call-back/{payment}', 'PaymentController@callback')->name('callback');

        Route::get('stripe/{payment}', 'StripeController@showForm')->name('stripe');
    });

Route::fallback('HomeController@fallback');
