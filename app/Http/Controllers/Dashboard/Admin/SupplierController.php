<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends AbstarctAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'suppliers';

    /**
     * @var array
     */
    protected $searchableFields = ['suppliers.name', 'users.email'];

    /**
     * @var string
     */
    protected $viewBasePath = 'supplier';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Supplier::query()
            ->leftJoin('users', 'users.id', '=', 'suppliers.user_id')
            ->select(['suppliers.*', 'users.email AS login_email']);
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

        if (Supplier::createNewSupplier($data, $insight)) {
            flash()->success($insight->message ?? __('Supplier :name was added successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.supplier.index');
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        $supplier->load(['user']);
        return $this->renderForm('dashboard.admin.supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Supplier $supplier
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function update(Request $request, Supplier $supplier)
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

        if ($supplier->updateInfo($data, $insight)) {
            flash()->success($insight->message ?? __('Supplier :name was updated successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.supplier.index');
        }
        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

}
