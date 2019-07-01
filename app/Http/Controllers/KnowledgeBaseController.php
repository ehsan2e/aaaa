<?php

namespace App\Http\Controllers;

use App\Post;
use App\PostCategory;

class KnowledgeBaseController extends Controller
{
    public function displayPost(int $id)
    {
      $post = Post::findOrFail($id);
      if($post->language === app()->getLocale()){
          $postCategories = PostCategory::activeLanguage()->active()->orderBy('name')->get();
          $activeCategory = $post->category_id;
          return view('knowledge-base.post', compact('postCategories', 'post', 'activeCategory'));
      }

      $posts = Post::where('code', $post->code)
          ->whereIn('language', [app()->getLocale(), config('app.fallback_locale')])->get();
      foreach ($posts as $post){
          if($post->language === app()->getLocale()){
              return redirect($post->permalink);
          }
      }
      $anotherLanguage = true;
      return [__FILE__, __LINE__] + compact('post', 'anotherLanguage');
    }

    public function displayPostCategory(int $id)
    {
      $postCategory = PostCategory::findOrFail($id);
      if($postCategory->language === app()->getLocale()){
          $postCategories = PostCategory::activeLanguage()->active()->orderBy('name')->get();
          $posts = Post::where('category_id', $postCategory->id)->active()->orderBy('created_at', 'desc')->paginate();
          $activeCategory = $postCategory->id;
          return view('knowledge-base.category', compact('postCategories', 'posts', 'activeCategory'));
      }

      $postCategories = PostCategory::where('code', $postCategory->code)
          ->whereIn('language', [app()->getLocale(), config('app.fallback_locale')])->get();
      foreach ($postCategories as $postCategory){
          if($postCategory->language === app()->getLocale()){
              return redirect($postCategory->permalink);
          }
      }
      $anotherLanguage = true;
      return [__FILE__, __LINE__] + compact('postCategory', 'anotherLanguage');
    }

    public function categoryIndex(PostCategory $category)
    {
        if(!$category->url){
            return $this->displayPostCategory($category->id);
        }

        return redirect($category->permalink);
    }

    public function index()
    {
        $postCategories = PostCategory::activeLanguage()->active()->orderBy('name')->get();
        $posts = Post::activeLanguage()->active()->orderBy('created_at', 'desc')->paginate();
        return view('knowledge-base.index', compact('postCategories', 'posts'));
    }

    public function post(Post $post)
    {
        if(!$post->url){
            return $this->displayPost($post->id);
        }

        return redirect($post->permalink);
    }
}
