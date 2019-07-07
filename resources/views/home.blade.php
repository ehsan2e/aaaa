@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Home page</div>

                <div class="card-body">
                    <a href="{{ route('hosted-pbx-session') }}" class="btn btn-success"><i class="fa fa-server"></i> {{ __('Order your hosted pbx') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection