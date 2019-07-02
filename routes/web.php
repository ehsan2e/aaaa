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
        ->group(function(){
            Route::prefix('client')
                ->namespace('Client')
                ->name('client.')
                ->middleware(['role.check:' . \App\Role::ROLE_CLIENT])
                ->group(function () {
                    Route::post('support/{ticket}/create-entry', 'SupportController@createEntry')
                        ->name('support.create-entry');
                    Route::resource('support', 'SupportController', ['except' => ['destroy', 'edit', 'update']])
                        ->parameter('support', 'ticket');
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
                            Route::resource('post', 'PostController', ['except' => ['show', 'destroy']]);
                            Route::resource('post-category', 'PostCategoryController', ['except' => ['show', 'destroy']]);
                        });

                    Route::get('gallery', 'MediaController@gallery')
                        ->name('gallery.index');

                    Route::get('gallery/upload-image', 'MediaController@showUploadImageForm')
                        ->name('gallery.upload-image');
                    Route::post('gallery/upload-image', 'MediaController@uploadImage');
                    Route::delete('gallery/{image}/delete', 'MediaController@deleteImage')
                        ->name('gallery.delete-image');
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
        Route::post('locked-screen', 'PanelController@unlockScreen');

    });

Route::fallback('HomeController@fallback');

Route::prefix('knowledge-base')
    ->name('knowledge-base.')
    ->group(function () {
        Route::get('/', 'KnowledgeBaseController@index')->name('index');
        Route::get('/category/{category}', 'KnowledgeBaseController@categoryIndex')->name('category');
        Route::get('/post/{post}', 'KnowledgeBaseController@post')->name('post');
    });