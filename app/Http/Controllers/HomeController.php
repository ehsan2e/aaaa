<?php

namespace App\Http\Controllers;

use App\CustomUrl;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    protected function commonPage(string $view)
    {
        $inOtherLanguage = false;
        $viewPath = sprintf('common-pages.%s.%s', app()->getLocale(), $view);
        if (!view()->exists($viewPath)) {
            $inOtherLanguage = true;
            $viewPath = sprintf('common-pages.%s.%s', config('app.fallback_locale'), $view);
        }
        return view($viewPath, compact('inOtherLanguage'));
    }

    public function aboutUs(){
        return $this->commonPage('about-us');
    }

    public function contactUs(){
        return view('contact-us');
    }

    public function faq(){
        return $this->commonPage('faq');
    }

    public function fallback(Request $request)
    {
        if (preg_match('#^media/image/([\w\-]+)(\.(\d+)x(\d+)|)\.(jpg|jpeg|png|gif)$#', $request->path(), $matches) === 1) {
            return app()->call('App\Http\Controllers\Dashboard\Admin\CMS\MediaController@compilePublicImage', ["{$matches[1]}.{$matches['5']}", empty($matches[3]) ? null : ((int)$matches[3]), empty($matches[4]) ? null : ((int)$matches[4])]);
        }
        $path = $request->path();
        /** @var CustomUrl $customUrl */
        $customUrl = \App\CustomUrl::where('path', $path)->orWhere('path', urldecode($path))->first();
        if (!$customUrl) {
            abort(404);
        }
        if ($customUrl->redirect_url) {
            return redirect($customUrl->target_url, $customUrl->redirect_status ?? 302);
        }

        if($customUrl->handler){
            return app()->call($customUrl->handler, $customUrl->parameters ?? []);
        }

        abort(500);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function privacyPolicy(){
        return $this->commonPage('privacy-policy');
    }

    public function termsAndServices()
    {
        return $this->commonPage('terms-and-services');
    }
}
