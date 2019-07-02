<?php

namespace App\Http\Controllers\Dashboard\Admin\CMS;

use App\CustomUrl;
use App\Facades\CustomUrlHandler;
use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\Rules\JsonObjectString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomUrlController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'customUrls';

    /**
     * @var array
     * since we need to use advanced features we overwrote the gerSearchableFields for this controller
     */
   // protected $searchableFields = [];

    /**
     * @var string
     */
    protected $viewBasePath = 'cms.custom-url';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return CustomUrl::query();
    }

    protected function getSearchableFields(): array
    {
        return [
            'path',
            'redirect_url',
            'redirect_status',
            function (Builder $query, string $q) {
                $seekingHandlers = [];
                $handlers = CustomUrlHandler::getHandlers();
                foreach ($handlers as $key => $value) {
                    if (stripos($value, $q) !== false) {
                        $seekingHandlers[] = $key;
                    }
                }
                $query->whereIn('handler', $seekingHandlers);
            }
        ];
    }

    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    protected function renderForm(string $view, array $data = [])
    {
        $handlers = CustomUrlHandler::getHandlers();
        uasort($handlers, function ($a, $b) {
            return $a <=> $b;
        });
        $redirectStatuses = CustomUrl::REDIRECT_STATUS_CODES;
        return view($view, compact('handlers', 'redirectStatuses') + $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $v = Validator::make($data, ['path' => ['required', Rule::unique('custom_urls')]]);
        $v->sometimes('redirect_url', 'required', function ($input) {
            return !isset($input->handler);
        });
        $v->sometimes('redirect_status', ['required', Rule::in(array_keys(CustomUrl::REDIRECT_STATUS_CODES))], function ($input) {
            return !isset($input->handler);
        });
        $v->sometimes('handler', ['required', Rule::in(array_keys(CustomUrlHandler::getHandlers()))], function ($input) {
            return (!isset($input->redirect_url)) && (!isset($input->redirect_status));
        });
        $v->sometimes('parameters', 'json', function ($input) {
            return isset($input->parameters, $input->handler);
        });
        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        }
        if (!is_null($url = CustomUrl::createNewCustomUrl($data))) {
            flash()->success(__('url :url was created successfully', ['url' => $url->url]));
            return redirect()->route('dashboard.admin.cms.custom-url.index');
        }

        flash()->error(__('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomUrl $customUrl
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomUrl $customUrl)
    {
        return $this->renderForm('dashboard.admin.cms.custom-url.edit', compact('customUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\CustomUrl $customUrl
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomUrl $customUrl)
    {
        $data = $request->all();
        $v = Validator::make($data, ['path' => ['required', Rule::unique('custom_urls')->ignore($customUrl->id)]]);
        $v->sometimes('redirect_url', 'required', function ($input) {
            return !isset($input->handler);
        });
        $v->sometimes('redirect_status', ['required', Rule::in(array_keys(CustomUrl::REDIRECT_STATUS_CODES))], function ($input) {
            return !isset($input->handler);
        });
        $v->sometimes('handler', ['required', Rule::in(array_keys(CustomUrlHandler::getHandlers()))], function ($input) {
            return (!isset($input->redirect_url)) && (!isset($input->redirect_status));
        });
        $v->sometimes('parameters', 'json', function ($input) {
            return isset($input->parameters, $input->handler);
        });
        if ($v->fails()) {
            return back()->withInput()->withErrors($v);
        }
        if ($customUrl->updateInfo($data)) {
            flash()->success(__('url :url was created successfully', ['url' => $customUrl->url]));
            return redirect()->route('dashboard.admin.cms.custom-url.index');
        }

        flash()->error(__('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomUrl $customUrl
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(CustomUrl $customUrl)
    {
        try {
            $customUrl->delete();
            flash()->success(__('Url was removed successfully'));
        } catch (QueryException $queryException) {
            if (stripos($queryException->getMessage(), 'integrity')) {
                flash()->error(__('Url is being used by cms and cannot be removed'));
            } else {
                flash()->error(__('An unknown error happened please try again later'));
            }
        }
        return redirect()->route('dashboard.admin.cms.custom-url.index');
    }
}
