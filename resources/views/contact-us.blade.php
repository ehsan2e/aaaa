@php
$pageTitle = __('Contact Us');
$pageDescription = __('Contact us description');
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center text-uppercase">{{ __('Contact Us') }}</h1>
        <p class="text-center w-75 m-auto">{{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris interdum purus at sem ornare sodales. Morbi leo nulla, pharetra vel felis nec, ullamcorper condimentum quam.') }}</p>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-3 my-5">
                <div class="card border-0">
                    <div class="card-body text-center">
                        <i class="fa fa-phone fa-5x mb-3" aria-hidden="true"></i>
                        <h4 class="text-uppercase mb-5">{{ __('Call Us') }}</h4>
                        <p>+8801683615582,+8801750603409</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 my-5">
                <div class="card border-0">
                    <div class="card-body text-center">
                        <i class="fa fa-map-marker fa-5x mb-3" aria-hidden="true"></i>
                        <h4 class="text-uppercase mb-5">{{ __('Office Location') }}</h4>
                        <address>Suite 02, Level 12, Sahera Tropical Center </address>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 my-5">
                <div class="card border-0">
                    <div class="card-body text-center">
                        <i class="fa fa-map-marker fa-5x mb-3" aria-hidden="true"></i>
                        <h4 class="text-uppercase mb-5">{{ __('Office Location') }}</h4>
                        <address>Suite 02, Level 12, Sahera Tropical Center </address>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 my-5">
                <div class="card border-0">
                    <div class="card-body text-center">
                        <i class="fa fa-envelope fa-5x mb-3" aria-hidden="true"></i>
                        <h4 class="text-uppercase mb-5">{{ __('Email') }}</h4>
                        <p>http://al.a.noman1416@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection