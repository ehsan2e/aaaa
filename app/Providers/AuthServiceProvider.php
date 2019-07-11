<?php

namespace App\Providers;

use App\CartItem;
use App\Order;
use App\Policies\CartItemPolicy;
use App\Policies\OrderPolicy;
use App\Policies\TicketPolicy;
use App\Ticket;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        CartItem::class => CartItemPolicy::class,
        Order::class => OrderPolicy::class,
        Ticket::class => TicketPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::before(function (?User $user, $ability, $params) {
            if ($user && ($user->isAble($ability))) {
                return true;
            }
        });
    }
}
