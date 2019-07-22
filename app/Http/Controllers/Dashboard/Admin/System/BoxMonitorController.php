<?php

namespace App\Http\Controllers\Dashboard\Admin\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BoxMonitorController extends Controller
{
    public function index()
    {
        $data = ['key' => 12];
        $wsUrl = config('nova.ws_url');
        $wsUrl .= (strpos($wsUrl, '?') === false ? '?' : '&') . http_build_query($data);
        return view('dashboard.admin.system.box-monitor.index', compact('wsUrl'));
    }
}
