<?php

namespace App\Http\Controllers\Dashboard\Admin\CMS;

use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\PostCategory;
use App\Rules\ValidLanguage;
use App\Rules\ValidParentCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use function NovaVoip\sortedLanguages;

class PostCategoryController extends AbstractAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'postCategories';

    /**
     * @var
     */
    protected $searchableFields = ['post_categories.code', 'post_categories.language', 'post_categories.name', 'post_categories.address', 'pc2.name'];

    /**
     * @var string
     */
    protected $viewBasePath = 'cms.post-category';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return PostCategory::with(['url'])
            ->leftJoin('post_categories AS pc2', 'pc2.id', '=', 'post_categories.parent_id')
            ->select(['post_categories.*', 'pc2.name as parent_name']);
    }

    /**
     * @param string $view
     * @param array $data
     * @param array $except
     * @return \Illuminate\Http\Response
     */
    protected function renderForm(string $view, array $data = [], array $except = [])
    {
        $languages = array_map(function($item){return $item['display'];}, sortedLanguages());
        $defaultLanguage = config('app.fallback_locale');
        /** @var Builder $builder */
        $builder = PostCategory::select('id', 'language', 'name');
        if(count($except) > 0){
            $builder->whereNotIn('id', $except);
            $builder->where(function($query1) use ($except){
                $query1->whereNull('address')->orWhere(function($query2) use ($except){
                    foreach ($except as $id){
                        $query2->where('address', 'Not LIKE', "%-{$id}-%");
                    }
                });
            });
        }
        $postCategories = $builder->orderBy('language')->orderBy('name')->get();
        $preparedPostCategories = [];
        foreach ($postCategories as $postCategory) {
            if (!isset($preparedPostCategories[$postCategory->language])) {
                $preparedPostCategories[$postCategory->language] = [];
            }
            $preparedPostCategories[$postCategory->language][] = $postCategory;
        }
        return parent::renderForm($view, compact('defaultLanguage', 'languages', 'preparedPostCategories') + $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'language' => ['required', new ValidLanguage()],
            'code' => ['required'],
            'name' => ['required'],
            'picture' => [],
            'active' => ['boolean'],
            'seo_link' => [
                Rule::unique('custom_urls', 'path'),
            ],
            'og_title' => [],
            'og_description' => [],
            'og_site_name' => [],
            'og_type' => [],
            'og_image' => [],
            'twitter_site' => [],
            'twitter_title' => [],
            'twitter_description' => [],
            'twitter_creator' => [],
            'twitter_card' => [],
            'twitter_url' => [],
            'twitter_image' => [],
        ]);
        $request->validate([
            'name' => [
                Rule::unique('post_categories')->where(function ($query) use ($data) {
                    return $query->where('language', $data['language']);
                }),
            ],
            'code' => [
                Rule::unique('post_categories')->where(function ($query) use ($data) {
                    return $query->where('language', $data['language']);
                }),
            ],
            'parent_id' => [new ValidParentCategory($data['language'])],
        ]);
        if (PostCategory::createNewCategory($data, $insight)) {
            flash()->success($insight->message ?? __('Post category :name was created successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.cms.post-category.index');
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PostCategory $postCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(PostCategory $postCategory)
    {
        $postCategory->load(['url', 'seoConfig']);
        return $this->renderForm('dashboard.admin.cms.post-category.edit', compact('postCategory'), [$postCategory->id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\PostCategory $postCategory
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function update(Request $request, PostCategory $postCategory)
    {
        $data = $request->all();
        $request->validate([
            'language' => ['required', new ValidLanguage()],
            'name' => ['required'],
            'code' => ['required'],
            'picture' => [],
            'active' => ['boolean'],
            'seo_link' => [
                Rule::unique('custom_urls', 'path')->ignore($postCategory->url_id ?? 0),
            ],
            'og_title' => [],
            'og_description' => [],
            'og_site_name' => [],
            'og_type' => [],
            'og_image' => [],
            'twitter_site' => [],
            'twitter_title' => [],
            'twitter_description' => [],
            'twitter_creator' => [],
            'twitter_card' => [],
            'twitter_url' => [],
            'twitter_image' => [],
        ]);
        $request->validate([
            'name' => [
                Rule::unique('post_categories')->where(function ($query) use ($data) {
                    return $query->where('language', $data['language']);
                })->ignore($postCategory->id),
            ],
            'code' => [
                Rule::unique('post_categories')->where(function ($query) use ($data) {
                    return $query->where('language', $data['language']);
                })->ignore($postCategory->id),
            ],
            'parent_id' => [(new ValidParentCategory($data['language']))->forbidden($postCategory->id)],
        ]);
        if ($postCategory->updateInfo($data, $insight)) {
            flash()->success($insight->message ?? __('Post category :name was updated successfully', ['name' => $data['name']]));
            return redirect()->route('dashboard.admin.cms.post-category.index');
        }
        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }
}
