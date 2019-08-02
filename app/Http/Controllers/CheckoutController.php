<?php

namespace App\Http\Controllers;

use App\Cart;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __invoke(Request $request)
    {
        /** @var Cart $cart */
        $cart = app('cart');
        if(!$cart){
            flash()->error(__('An unknown error happened please try again later'));
            return redirect()->route('cart');
        }

        if(count($cart->items) === 0){
            flash()->error(__('Your cart is empty'));
            return redirect()->route('cart');
        }

        if(!isset($cart->country_code,$cart->province_code)){
            flash()->error(__('Please determine your tax region'));
            return redirect()->route('cart');
        }

        if(($order = $cart->createOrder(true, false, $insight)) === null){
            flash()->error($insight->message ?? __('An unknown error happened please try again later'));
            return redirect()->route('cart');
        }

        $request->session()->forget('cart_id');
        return redirect()->route('dashboard.client.order.show', ['order' => $order]);

    }
}
