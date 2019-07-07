@php $extended = $extended ?? false @endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Cart') }}@if(app()->environment('development'))
                            #{{$cart->id}}@endif</div>
                    @if(count($cart->items) === 0)
                        <div class="card-body">
                            <div class="alert alert-warning"><i
                                        class="fa fa-warning"></i> {{ __('Your cart is empty!') }}</div>
                        </div>
                    @else
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Price') }}
                                    <small>({{ $systemCurrencyCode }})</small>
                                </th>
                                <th>{{ __('Sub Total') }}
                                    <small>({{ $systemCurrencyCode }})</small>
                                </th>
                                @if($extended)
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Tax') }}</th>
                                    <th>{{ __('Grand Total') }}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @component('cart.row', ['items' => $cart->items, 'extended' => $extended]) @endcomponent
                            @if($extended)
                                <tr class="cart-invoice-summary-row first-row">
                                    <th colspan="4"></th>
                                    <th>{{ $cart->sub_total }}</th>
                                    <th>{{ $cart->discount }}</th>
                                    <th>{{ $cart->tax }}</th>
                                    <th>{{ $cart->grand_total }}</th>
                                </tr>
                            @else
                                <tr class="cart-invoice-summary-row first-row">
                                    <th colspan="3"></th>
                                    <th class="text-right">{{ __('Sub Total') }}</th>
                                    <th>{{ $cart->sub_total }}</th>
                                </tr>
                                <tr class="cart-invoice-summary-row">
                                    <td colspan="3"></td>
                                    <th class="text-right">{{ __('Discount') }}</th>
                                    <th>{{ $cart->discount }}</th>
                                </tr>
                                <tr class="cart-invoice-summary-row">
                                    <th colspan="3"></th>
                                    <th class="text-right">{{ __('Tax') }}</th>
                                    <th>{{ $cart->tax }}</th>
                                </tr>
                                <tr class="cart-invoice-summary-row first-row">
                                    <th colspan="3" style="border-top:none"></th>
                                    <th class="text-right">{{ __('Grand Total') }}</th>
                                    <th>{{ $cart->grand_total }}</th>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <hr>
                        <div class="card-body clearfix">
                            <div class="pull-right">
                                <a href="#" class="btn btn-success"><i class="fa fa-check"></i> {{ __('Checkout') }}</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @unless(count($cart->items) === 0)
            <div class="row justify-content-center mt-3">

                <!-- Redeem voucher section -->
                <div class="col-lg-6 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-header">{{ __('Voucher') }}</div>
                        <div class="card-body">
                            <p>{{ __('Have a voucher? redeem your voucher here') }}</p>
                            <form action="{{ route('redeem-voucher') }}" method="post">
                                @csrf
                                <div class="form-group row">
                                    <label for="voucher_code" class="col-md-4 col-form-label text-md-right"
                                    >{{ __('Voucher Code') }}</label>
                                    <div class="col-md-8">
                                        <input class="form-control @error('voucher_code') is-invalid @enderror"
                                               type="text" id="voucher_code" name="voucher_code"
                                               value="{{ old('voucher_code', '') }}">
                                        @error('voucher_code')
                                        <span class="invalid-feedback custom-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row ">
                                    <div class="col-md-8 offset-md-4">
                                        <button class="btn btn-primary ">{{ __('Apply voucher') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tax region section -->
                <div class="col-lg-6 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-header">{{ __('Tax Region') }}</div>
                        <div class="card-body">
                            <p>{{ __('Select your tax region to get a more precise price') }}</p>
                            <form action="{{ route('cart-tax-region') }}" method="post">
                                @csrf
                                <div class="form-group row">
                                    <label for="country_code"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Country') }}</label>
                                    <div class="col-md-8">
                                        <select id="country_code" name="country_code"
                                                class="form-control chosen-select @error('country_code') is-invalid @enderror"
                                        >
                                            @component('dashboard.components.option', ['value'=>'', 'selectedValue' => old('country_code', $cart->country_code)]){{ __('Select Country') }}@endcomponent
                                            @foreach($countries as $country)
                                                @component('dashboard.components.option', ['value'=>$country->code, 'selectedValue' => old('country_code', $cart->country_code)]){{ \NovaVoip\translateEntity($country) }}@endcomponent
                                            @endforeach
                                        </select>
                                        @error('country_code')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="province_code"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Province') }}</label>
                                    <div class="col-md-8">
                                        <select id="province_code" name="province_code"
                                                class="form-control chosen-select @error('province_code') is-invalid @enderror"
                                        >
                                            @component('dashboard.components.option', ['value'=>'', 'selectedValue' => old('province_code', $cart->province_code)]){{ __('Select Province') }}@endcomponent
                                            @foreach($countries as $country)
                                                <optgroup label="{{ \NovaVoip\translateEntity($country) }}">
                                                    @foreach($country->provinces as $province)
                                                        @component('dashboard.components.option', ['value'=>$province->code, 'selectedValue' => old('province_code', $cart->province_code)]){{ \NovaVoip\translateEntity($province) }}@endcomponent
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        @error('province_code')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <button class="btn btn-primary ">{{ __('Update tax') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endunless
    </div>
@endsection

@push('before-body-ends')
    <form action="" method="post" id="remove-item-from-cart-form">
        @csrf
    </form>
    <script>
        (function ($) {
            $('.remove-item-from-cart').on('click', function (event) {
                event.preventDefault();
                $('#remove-item-from-cart-form').attr('action', $(this).attr('href')).submit();
            })
        })(jQuery);
    </script>
@endpush