<?php

namespace App\Http\Controllers\Dashboard\Admin\CMS;

use App\Http\Controllers\Dashboard\Admin\AbstarctAdminController;
use App\Post;
use App\PostCategory;
use App\Rules\ValidLanguage;
use App\Rules\ValidParentCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use function NovaVoip\sortedLanguages;

class PostController extends AbstarctAdminController
{
    /**
     * @var string
     */
    protected $collectionName = 'posts';

    /**
     * @var
     */
    protected $searchableFields = ['posts.code', 'posts.language', 'posts.title', 'post_categories.name', 'users.name'];

    /**
     * @var string
     */
    protected $viewBasePath = 'cms.post';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Post::with(['url'])
            ->leftJoin('post_categories', 'post_categories.id', '=', 'posts.category_id')
            ->leftJoin('users', 'users.id', '=', 'posts.author_id')
            ->select(['posts.id', 'posts.category_id', 'posts.author_id', 'posts.url_id', 'posts.code', 'posts.language', 'posts.title', 'posts.active', 'posts.disclose_author', 'post_categories.name as category_name', 'users.name as author_name']);
    }

    /**
     * @param string $view
     * @param array $data
     * @param null $language
     * @return \Illuminate\Http\Response
     */
    protected function renderForm(string $view, array $data = [], $language=null)
    {
        $languages = array_map(function($item){return $item['display'];}, sortedLanguages());
        $defaultLanguage = config('app.fallback_locale');
        /** @var Builder $builder */
        $builder = PostCategory::select('id', 'language', 'name');
        if(isset($language)){
            $builder->where('language', $language);
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
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'language' => ['required', new ValidLanguage()],
            'code' => ['required'],
            'title' => ['required'],
            'picture' => [],
            'excerpt' => ['required'],
            'content' => ['required'],
            'active' => ['boolean'],
            'disclose_author' => ['boolean'],
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
            'title' => [
                Rule::unique('posts')->where(function ($query) use ($data) {
                    return $query->where('language', $data['language']);
                }),
            ],
            'code' => [
                Rule::unique('posts')->where(function ($query) use ($data) {
                    return $query->where('language', $data['language']);
                }),
            ],
            'category_id' => [new ValidParentCategory($data['language'])],
        ]);
        if (Post::createNewPost(Auth::user(), $data, $insight)) {
            flash()->success($insight->message ?? __('Post :title was created successfully', ['title' => $data['title']]));
            return redirect()->route('dashboard.admin.cms.post.index');
        }

        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $post->load(['url', 'seoConfig']);
        return $this->renderForm('dashboard.admin.cms.post.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->all();
        $request->validate([
            'language' => ['required', new ValidLanguage()],
            'code' => ['required'],
            'title' => ['required'],
            'picture' => [],
            'excerpt' => ['required'],
            'content' => ['required'],
            'active' => ['boolean'],
            'disclose_author' => ['boolean'],
            'seo_link' => [
                Rule::unique('custom_urls', 'path')->ignore($post->url_id ?? 0),
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
            'title' => [
                Rule::unique('posts')->where(function ($query) use ($data) {
                    return $query->where('language', $data['language']);
                })->ignore($post->id),
            ],
            'code' => [
                Rule::unique('posts')->where(function ($query) use ($data) {
                    return $query->where('language', $data['language']);
                })->ignore($post->id),
            ],
            'category_id' => [new ValidParentCategory($data['language'])],
        ]);
        if ($post->updateInfo(Auth::user(), $data, $insight)) {
            flash()->success($insight->message ?? __('Post :title was updated successfully', ['title' => $data['title']]));
            return redirect()->route('dashboard.admin.cms.post.index');
        }
        flash()->error($insight->message ?? __('An unknown error happened please try again later'));
        return back()->withInput();
    }
}
