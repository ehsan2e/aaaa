{{--{{ print_r(\App\Facades\UIManager::getActivePath(),true) }}--}}
@php
    $menu = require base_path('resources/views/dashboard/partials/client-menu-config.php');
@endphp
@foreach($menu as $item)
    @component('dashboard.partials.menu-item', ['item' => $item, 'level' => 0])@endcomponent
@endforeach