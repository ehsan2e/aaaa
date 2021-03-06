<?php

namespace App\Http\Controllers\Dashboard\Admin\CRM;

use App\Ability;
use App\Client;
use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'clients';

    /**
     * @var array
     */
    protected $searchableFields = ['users.email'];

    /**
     * @var string
     */
    protected $viewBasePath = 'crm.client';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Client::query()
            ->leftJoin('users', 'users.id', '=', 'clients.user_id')
            ->select(['clients.*', 'users.email AS login_email']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $v = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'active' => ['boolean'],
            'create_account' => ['boolean'],
        ]);

        $v->sometimes('email', ['required', 'string', 'email', 'max:255', 'unique:users'], function($input){
            return isset($input->create_account);
        });

        $v->sometimes('password', ['required', 'string', 'min:8', 'confirmed'], function($input){
            return isset($input->create_account);
        });

        if($v->fails()){
            return back()->withInput()->withErrors($v);
        }

        if (Client::createNewClient($data, $insight)) {
            flash()->success($insight->message ?? __('Client :name was added successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.crm.client.index');
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        dd(__FILE__, __LINE__, 'not implemented');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        $client->load(['user']);
        return $this->renderForm('dashboard.admin.crm.client.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * @param Request $request
     * @param Client $client
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function loginAs(Request $request, Client $client)
    {
        $request->validate([
            'password' => 'required',
        ]);
        $this->authorize(Ability::LOGIN_AS_CLIENT);
        if(!$client->user_id){
            flash()->error(__('Client does not have an account'));
            return back();
        }
        if(!Hash::check($request->password, $client->user->password)){
            flash()->error(__('Enter your password correctly'));
            return back();
        }
        $adminId = Auth::id();
        Auth::loginUsingId($client->user_id);
        $request->session()->put('admin_id', $adminId);
        return redirect()->route('dashboard.panel');
    }
}
