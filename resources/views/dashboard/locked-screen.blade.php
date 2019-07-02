@extends('layouts.empty')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Unlock Screen') }}</span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <form action="" method="post">
                            @csrf
                            @component('dashboard.components.password', ['name' => 'password', 'required' => true, 'autofocus' => true]){{ __('Password') }}@endcomponent
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Unlock') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection