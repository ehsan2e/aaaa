<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use NovaVoip\Helpers\PaymentGatewayResolver;
use NovaVoip\PaymentGateways\FakeGateway;
use NovaVoip\PaymentGateways\StripeGateway;

class PaymentGatewayProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /** @var PaymentGatewayResolver $paymentGatewayResolver */
        $paymentGatewayResolver = app(PaymentGatewayResolver::class);
        $paymentGatewayResolver->register('stripe', new StripeGateway(), __('Credit Card'));
        if(app()->isLocal()){
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
