<?php

namespace App\Http\Controllers;

use App\Ability;
use App\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    protected function a(Request $request)
    {
        return Attachment::get();
        $this->authorize('mama');
        return Auth::user()->load('roles.abilities');
    }

    public function __invoke(Request $request, string $method)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}($request);
        }
        return abort(404);
    }
}
