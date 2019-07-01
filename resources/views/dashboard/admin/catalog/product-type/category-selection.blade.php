@php \App\Facades\UIManager::setActivePath('catalog', 'product-types') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Create Product') }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.admin.cms.post.index') }}">{{ __('Back') }}</a>
                            </span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <form action="" method="get">
                            @component('dashboard.components.select', ['name' => 'category', 'model' => $productType ?? null])
                                @slot('options')
                                    <option value="">{{ __('Without category') }}</option>
                                    @foreach($productCategories as $productCategory)
                                        @component('dashboard.components.option', ['value'=>$productCategory->id, 'selectedValue' => old('category_id', $productType->category_id ?? '')]){{ $productCategory->name }}@endcomponent
                                    @endforeach
                                @endslot
                                {{ __('Category') }}
                            @endcomponent

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Select Category') }}
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


