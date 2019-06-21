@php
    $inputs = [
        'og_title' => __('OG Title'),
        'og_description' => __('OG Description'),
        'og_site_name' => __('OG Site Name'),
        'og_type' => __('OG Type'),
        'og_image' => __('OG Image'),
        'twitter_site' => __('Twitter Site'),
        'twitter_title' => __('Twitter Title'),
        'twitter_description' => __('Twitter Description'),
        'twitter_creator' => __('Twitter Creator'),
        'twitter_card' => __('Twitter Card'),
        'twitter_url' => __('Twitter Url'),
        'twitter_image' => __('Twitter Image'),
    ];
@endphp
@foreach($inputs as $c => $v)
    @component('dashboard.components.input-box', ['name' => $c, 'model' => $seoConfig ?? null]){{ $v }}@endcomponent
@endforeach