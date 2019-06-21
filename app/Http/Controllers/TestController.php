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
        $result = supervisedTransaction(function($insight){
            $insight->message = 'adaf';
            throw new SupervisedTransactionException();
            return true;
        }, false, false, true, $insight);
        dd($result, $insight);
        return Auth::user()->role->abilities;
    }

    public function index(Request $request, string $method)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}($request);
        }
        return abort(404);
    }
}
