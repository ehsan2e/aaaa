<?php

namespace App\Http\Controllers;

use App\Cart;
use App\CartItem;
use App\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function removeItem(CartItem $cartItem)
    {
        /** @var Cart $cart */
        $cart = app('cart');
        if(($cart->id == $cartItem->cart_id) && (!$cart->removeItem($cartItem))){
            flash()->error(__('An unknown error happened please try again later'));
        }
        return back();
    }

    public function showCart(Request $request)
    {
        $cart = app('cart');
        if(!$cart){
            abort(500);
        }
        $request->session()->put('cart_id', $cart->id);
        $cart->load(['items.productType.category']);
        $countries = Country::with(['provinces'])->orderBy('name')->get();
        $extended = config('nova.extended_invoice') ?? config('app.debug');
        return view('cart.index', compact('cart', 'countries', 'extended'));
    }

    public function redeemVoucher()
    {

    }

    public function taxRegion(Request $request)
    {
        /** @var Cart $cart */
        $cart = app('cart');
        if($cart->updateTaxRegion($request->country_code, $request->province_code)){
            flash()->success(__('Tax calculation updated according to your region'));
        }else{
            flash()->error(__('An unknown error happened please try again later'));
        }

        return back();
    }

}
