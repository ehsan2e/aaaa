<?php
// Template
/*
[
    'label' => __(''),
    'icon' => null,
    'identifier' => '',
    'href' => '#',
    'ability' => null,
    'children' => [],
],
*/
return [
    [
        'label' => __('Boxes'),
        'icon' => null,
        'identifier' => 'boxes',
        'href' => '#',
        'ability' => null,
        'children' => [],
    ],
    [
        'label' => __('Invoices'),
        'icon' => null,
        'identifier' => 'invoices',
        'href' => route('dashboard.client.invoice.index'),
        'ability' => null,
        'children' => [],
    ],
    [
        'label' => __('Orders'),
        'icon' => null,
        'identifier' => 'orders',
        'href' => route('dashboard.client.order.index'),
        'ability' => null,
        'children' => [],
    ],
    [
        'label' => __('Payments'),
        'icon' => null,
        'identifier' => 'payments',
        'href' => route('dashboard.client.payment.index'),
        'ability' => null,
        'children' => [],
    ],
    [
        'label' => __('Support'),
        'icon' => null,
        'identifier' => 'support',
        'href' => route('dashboard.client.support.index'),
        'ability' => null,
        'children' => [],
    ],
    [
        'label' => __('Wallet'),
        'icon' => null,
        'identifier' => 'wallet',
        'href' => route('dashboard.client.wallet'),
        'ability' => null,
        'children' => [],
    ],
];