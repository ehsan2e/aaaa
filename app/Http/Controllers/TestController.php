<?php

namespace App\Http\Controllers;

use App\User;
use Emad\Plugins\Ttm\Models\Escalation;
use Emad\Plugins\Ttm\Models\Process;
use Emad\Plugins\Ttm\Models\ProcessType;
use Illuminate\Http\Request;
use Emad\Service\SoapTypes\AttributeHashMap;
use Illuminate\Support\Facades\Auth;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;

class TestController extends Controller
{
    protected function a(Request $request)
    {
        $this->authorize('mama');
        return Auth::user()->load('roles.abilities');
    }

    public function index(Request $request, string $method)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}($request);
        }
        return abort(404);
    }
}
