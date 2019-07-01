@php
    $knowledgeBaseTitle = ($knowledgeBaseTitle ?? trans('knowledge-base.title')) . ' | '. config('app.name', 'Laravel');
    $knowledgeBaseDescription = $knowledgeBaseDescription ?? trans('knowledge-base.description');
    $knowledgeBaseImage = $knowledgeBaseImage ?? $defaultKnowledgeBaseImage ?? '';
    $knowledgeBaseCanonicalUrl = $knowledgeBaseCanonicalUrl ?? \Illuminate\Support\Facades\Request::fullUrl()
@endphp

@extends('layouts.knowledge-base')

@section('meta')
    <meta name="og:title" content="{{ $knowledgeBaseTitle }}">
    <meta name="og:description" content="{{ $knowledgeBaseDescription }}">
    <meta name="og:site_name" content="{{ config('app.name', 'Laravel') }}">
    <meta name="og:type" content="article">
    <meta name="og:image" content="{{ $knowledgeBaseImage }}">

    <meta name="twitter:site" content="{{ config('app.name', 'Laravel') }}">
    <meta name="twitter:title" content="{{ $knowledgeBaseTitle }}">
    <meta name="twitter:description" content="{{ $knowledgeBaseDescription }}">
    <meta name="twitter:creator" content="{{ config('app.name', 'Laravel') }}">
    <meta name="twitter:card" content="photo">
    <meta name="twitter:url" content="{{ $knowledgeBaseCanonicalUrl }}">
    <meta name="twitter:image" content="{{ $knowledgeBaseImage }}">
@endsection

@section('menu')
    @component('knowledge-base.partials.menu', compact('postCategories', 'activeCategory'))@endcomponent
@endsection

@section('content')
    @component('knowledge-base.partials.post-listing', compact('posts'))@endcomponent
@endsection
