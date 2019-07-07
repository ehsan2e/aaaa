<?php

namespace App\Http\Controllers;


use App\Cart;
use App\CartItem;
use Illuminate\Http\Request;
use NovaVoip\Helpers\Box;
use NovaVoip\Helpers\BoxService;

class HostedPBXSessionController extends Controller
{
    /**
     * @param Request $request
     * @param CartItem|null $cartItem
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    protected function addToCart(Request $request, CartItem $cartItem = null)
    {
        $request->validate([
            'employee_number' => ['required', 'integer'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
            'box_services' => ['nullable', 'array'],
            'domain' => ['required', 'string'],
        ]);

        /** @var Cart $cart */
        $cart = app('cart');

        if($cartItem){
            if($cart->id != $cartItem->cart_id){
                return redirect()->route('hosted-pbx-session');
            }
        }

        if (Box::addToCart($cart, (int) $request->employee_number, $request->admin_password, $request->domain, $request->box_services ?? [], $cartItem)) {
            flash()->success(__('Box was added to your cart'));
            return redirect()->route('cart');
        }

        flash()->error(__('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * @param CartItem|null $cartItem
     * @return \Illuminate\Http\Response
     */
    protected function configureBox(CartItem $cartItem = null)
    {
        $currentBox = null;
        if($cartItem){
            $cart = app('cart');
            if($cart->id != $cartItem->cart_id){
                return redirect()->route('hosted-pbx-session');
            }
            $currentBox = $cartItem->extra_information;
            $currentBox['box_services'] = $cartItem->children()->pluck('id')->toArray();
        }
        $boxes = Box::flattenedBoxes();
        $boxServices = BoxService::load();
        $employeeNumber = (int)old('employee_number', '1');
        $box = Box::resolveBox($employeeNumber);
        return view('hosted-pbx-session.configure-box', compact('box', 'boxes', 'boxServices', 'currentBox'));

    }
}