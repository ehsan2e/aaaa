@extends('layouts.empty')

@section('content')
    <form action="{{ $action ?? '' }}" method="{{ $method ?? 'post' }}" id="forward-form">
        @foreach($params ?? [] as $name => $value)
            <input type="hidden" name="{{$name}}" value="{{$value}}">
        @endforeach
    </form>
@endsection

@push('before-body-ends')
    <script>
        document.getElementById('forward-form').submit();
    </script>
@endpush