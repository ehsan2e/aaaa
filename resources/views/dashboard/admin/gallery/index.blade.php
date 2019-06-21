@php \App\Facades\UIManager::setActivePath('cms', 'gallery') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @component('dashboard.components.search-list', ['id' => 'gallery', 'placeholder' => __('Search image'), 'queryError' => $queryError, 'canRunRawQuery' => $canRunRawQuery])
                    <a class="btn btn-success"
                       href="{{ route('dashboard.admin.gallery.upload-image') }}">{{ __('Upload Image') }}</a>
                @endcomponent
                @if(count($images) > 0)
                    <div class="row text-center text-lg-left">
                        @foreach($images as $image)
                            <div class="col-lg-3 col-md-4 col-6">
                                <div class="h-100">
                                    <div class="btn-group d-flex">
                                        <button class="btn btn-outline-secondary btn-copy"
                                                data-clipboard-text="{{ $image->name }}">
                                            {{ __('Copy name') }}</button>
                                        <button class="btn btn-outline-secondary btn-copy"
                                                data-clipboard-text="{{ $image->getUrl() }}">
                                            {{ __('Copy Url') }}</button>
                                        <a class="btn btn-danger"
                                           href="Javascript:removeItem('{{ route('dashboard.admin.gallery.delete-image', ['image' => $image->id]) }}', '{{ __('Are you sure that you want to remove :name', ['name' => $image->name]) }}')"
                                        >{{ __('Delete') }}</a>
                                    </div>
                                    <a href="{{ $image->getUrl() }}" target="_blank" class="d-block">
                                        <img class="img-fluid img-thumbnail" src="{{ $image->getUrl() }}" alt="">
                                    </a>
                                    <div class="text-center">{{ $image->name }}
                                        ({{ $image->width }}&times;{{ $image->height }})
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {!! $images->links() !!}
                @else
                    <div class="text-center">{{ __('No image were found') }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection