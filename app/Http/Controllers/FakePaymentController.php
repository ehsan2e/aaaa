<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FakePaymentController extends Controller
{
    public function landing(Request $request)
    {
        $data = json_decode(base64_decode($request->token));
        $id = md5(random_bytes(12));
        $request->session()->put('payment_'.$id, $data);
        return redirect()->route('fake-payment.payment', ['session_id' => $id]);
    }

    public function payment(Request $request)
    {
        $data = $request->session()->get('payment_' . $request->session_id ?? '');
        if(!$data){
            abort(404);
        }
        $r = ['amount' => $data->amount, 'id' => $data->id , 'paid' =>$request->decision === 'yes', 'reference' => uniqid()];
        $url = $data->return_url . (strpos($data->return_url, '?') === false ? '?' : ':')  . http_build_query(['r' => base64_encode(json_encode($r))]);
        return redirect($url);
    }

    public function paymentForm(Request $request)
    {
        $data = $request->session()->get('payment_' . $request->session_id ?? '');
        if(!$data){
            abort(404);
        }
        return view('fake-payment', compact('data'));
    }

    public function request(Request $request)
    {
        return response()->json(['token' => base64_encode(json_encode($request->toArray()))]);
    }

    public function verify(Request $request)
    {
        return response()->json(json_decode(base64_decode($request->r ?? base64_encode('{"mount": null, "id": null, "paid": false}'))));

    }
}
