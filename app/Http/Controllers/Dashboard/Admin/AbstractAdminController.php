<?php

namespace App\Http\Controllers\Dashboard\Admin;


use App\Http\Controllers\Dashboard\AbstractDashboardController;

abstract class AbstractAdminController extends AbstractDashboardController{
    protected $dashboardPrefix = 'dashboard.admin';
}