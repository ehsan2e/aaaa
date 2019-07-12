<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use NovaVoip\Helpers\PaymentGatewayResolver;
use NovaVoip\PaymentGateways\FakeGateway;

class PaymentGatewayProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if(app()->isLocal()){
            /** @var PaymentGatewayResolver $paymentGatewayResolver */
            $paymentGatewayResolver = app(PaymentGatewayResolver::class);
            $paymentGatewayResolver->register('fake', new FakeGateway(), __('Fake gateway'));
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaymentGatewayResolver::class, function(){
            return new PaymentGatewayResolver();
        });
    }
}
