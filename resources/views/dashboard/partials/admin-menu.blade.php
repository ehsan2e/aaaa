{{--{{ print_r(\App\Facades\UIManager::getActivePath(),true) }}--}}
@php
    $menu = \App\Facades\UIManager::prepareMenu(require base_path('resources/views/dashboard/partials/admin-menu-config.php'));
@endphp
@foreach($menu as $item)
    @component('dashboard.partials.menu-item', ['item' => $item, 'level' => 0])@endcomponent
@endforeach