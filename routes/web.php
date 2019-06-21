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
    Route::any('test/{method}', 'TestController@index')->name('test');
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
        Route::prefix('admin')
            ->namespace('Admin')
            ->name('admin.')
            ->middleware(['role.check:' . \App\Role::ROLE_ADMIN])
            ->group(function () {
                Route::resource('supplier', 'SupplierController');

                Route::prefix('catalog')
                    ->namespace('Catalog')
                    ->name('catalog.')
                    ->group(function(){
                        Route::resource('product-category', 'ProductCategoryController', ['except' => ['show', 'destroy']]);
                        Route::resource('product-type', 'ProductCategoryController', ['except' => ['show', 'destroy']]);
                    });

                Route::prefix('cms')
                    ->namespace('CMS')
                    ->name('cms.')
                    ->group(function(){
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
        Route::get('/profile', 'PanelController@showProfileForm')
            ->name('profile');
        Route::post('/profile', 'PanelController@profile');
        Route::get('/change-password', 'PanelController@showChangePasswordForm')
            ->name('change-password');
        Route::post('/change-password', 'PanelController@changePassword');
    });

Route::fallback('HomeController@fallback');

Route::prefix('knowledge-base')
    ->name('knowledge-base.')
    ->group(function () {
        Route::get('/', 'KnowledgeBaseController@index')->name('index');
        Route::get('/category/{category}', 'KnowledgeBaseController@categoryIndex')->name('category');
        Route::get('/post/{post}', 'KnowledgeBaseController@postIndex')->name('post');
    });