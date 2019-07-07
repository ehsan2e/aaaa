<?php

namespace App\Policies;

use App\Cart;
use App\User;
use App\CartItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartItemPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any cart items.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the cart item.
     *
     * @param  \App\User  $user
     * @param  \App\CartItem  $cartItem
     * @return mixed
     */
    public function view(User $user, CartItem $cartItem)
    {
        //
    }

    /**
     * Determine whether the user can create cart items.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the cart item.
     *
     * @param  \App\User  $user
     * @param  \App\CartItem  $cartItem
     * @return mixed
     */
    public function update(User $user, CartItem $cartItem)
    {
        //
    }

    /**
     * Determine whether the user can delete the cart item.
     *
     * @param  \App\User $user
     * @param  \App\CartItem $cartItem
     * @param Cart|null $cart
     * @return mixed
     */
    public function delete(User $user, CartItem $cartItem, Cart $cart=null)
    {
        return ($cart && ($cart->id === $cartItem->cart_id)) || $user->can('remove-item-from-cart');
    }

    /**
     * Determine whether the user can restore the cart item.
     *
     * @param  \App\User  $user
     * @param  \App\CartItem  $cartItem
     * @return mixed
     */
    public function restore(User $user, CartItem $cartItem)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the cart item.
     *
     * @param  \App\User  $user
     * @param  \App\CartItem  $cartItem
     * @return mixed
     */
    public function forceDelete(User $user, CartItem $cartItem)
    {
        //
    }
}
