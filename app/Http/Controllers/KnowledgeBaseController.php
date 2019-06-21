<?php

namespace App\Http\Controllers;

use App\PostCategory;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function displayPostCategory(int $id)
    {
      $postCategory = PostCategory::findOrFail($id);
      if($postCategory->language === app()->getLocale()){
        return $postCategory;
      }

      $postCategories = PostCategory::where('code', $postCategory->code)
          ->whereIn('language', [app()->getLocale(), config('app.fallback_locale')])->get();
      foreach ($postCategories as $postCategory){
          if($postCategory->language === app()->getLocale()){
              return redirect($postCategory->permalink);
          }
      }
      $anotherLanguage = true;
      return compact('postCategory', 'anotherLanguage');
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
        return view('knowledge-base.index');
    }
}
