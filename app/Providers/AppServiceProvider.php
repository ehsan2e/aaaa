<?php

namespace App\Providers;

use App\Cart;
use App\Facades\CustomUrlHandler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use NovaVoip\Helpers\CustomUrlHandler as CustomUrlHandlerRegistrar;
use NovaVoip\Helpers\UIManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cart', function () {
            return Cart::loadCart(Auth::user(), Request::session()->getId());
        });

        $this->app->singleton('custom-url-handler', function () {
            return new CustomUrlHandlerRegistrar();
        });

        $this->app->singleton('ui-manager', function () {
            return new UIManager();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('date', function ($expression) {
            list($date, $format) = array_pad(explode(',', $expression), 2, null);
            $format = $format ?? '\'Y-m-d\'';
            return "<?php {$date} = is_string({$date}) ? Carbon\Carbon::createFromTimestamp(strtotime({$date})) : {$date}; echo {$date}->format({$format}) ?>";
        });
        Blade::directive('priceOrFreeOfCharge', function ($expression) {
            return "<?php echo ({$expression} > 0) ? ((1 * {$expression}) . (\$systemCurrencyCode ?? config('nova.currency_code'))) : e(__('Free of Charge')) ?>";
        });
        Blade::directive('inactivepath', function ($expression) {
            return "<?php if(\\App\\Facades\\UIManager::isInActivePath({$expression})): ?>";
        });
        Blade::directive('inactivepath', function ($expression) {
            $parts = explode(',', $expression);
            $class = array_splice($parts, count($parts) - 1)[0];
            $path = implode(',', $parts);
            return "<?php if(\\App\\Facades\\UIManager::isInActivePath({$path})): echo e({$class}); endif; ?>";
        });
        array_map(function ($type) {
            Blade::directive($type, function ($expression) use ($type) {
                return '<?php if(Auth::user()->is' . ucfirst($type) . '()): ?>';
            });
            Blade::directive('end' . $type, function () {
                return '<?php endif; ?>';
            });
        }, ['admin', 'client', 'supplier']);
        CustomUrlHandler::add('\\App\\Http\\Controllers\\KnowledgeBaseController@displayPostCategory', __('Post Category Handler'))
            ->add('\\App\\Http\\Controllers\\KnowledgeBaseController@displayPost', 'Post Handler');

        Blade::directive('someError', function ($expression) {
            return "<?php if(array_reduce({$expression}, function(\$hasError, \$name) use (\$errors){return \$hasError || (strpos(\$name, '*') === false ? \$errors->has(\$name) : (preg_match('/' . str_replace(['.', '*'], ['\\.', '\\d+'], \$name) .'/', implode('|', array_keys(\$errors->toArray()))) === 1));}, false)): ?>";
        });
        Blade::directive('endSomeError', function ($expression) {
            return '<?php endif; ?>';
        });

        if (!config('nova.ignore_recaptcha')) {
            ViewFacade::composer(
                ['auth.login', 'auth.register', 'auth.verify', 'auth.passwords.email', 'auth.passwords.reset', 'dashboard.locked-screen'],
                function (View $view) {
                    $view->with('usesRecaptcha', true);
                }
            );
        }

        \Illuminate\Support\Facades\View::share('systemCurrencyCode', config('nova.currency_code'));

    }
}
