<?php

namespace App\Http\Controllers\Dashboard\Client;


use App\Http\Controllers\Dashboard\AbstractDashboardController;

abstract class AbstarctClientController extends AbstractDashboardController
{
    protected $dashboardPrefix = 'dashboard.client';
}