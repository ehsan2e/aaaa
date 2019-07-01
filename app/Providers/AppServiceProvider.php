<?php

namespace App\Providers;

use App\Facades\CustomUrlHandler;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
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
        $this->app->singleton('custom-url-handler', function() {
            return new CustomUrlHandlerRegistrar();
        });

        $this->app->singleton('ui-manager', function(){
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
        Blade::directive('date', function($expression){
            list($date, $format) = array_pad(explode(',', $expression), 2, null);
            $format = $format ?? '\'Y-m-d\'';
            return  "<?php echo {$date}->format({$format}) ?>";
        });
        Blade::directive('inactivepath', function($expression){
            return "<?php if(\\App\\Facades\\UIManager::isInActivePath({$expression})): ?>";
        });
        Blade::directive('inactivepath', function($expression){
            $parts = explode(',', $expression);
            $class = array_splice($parts, count($parts)- 1)[0];
            $path = implode(',', $parts);
            return "<?php if(\\App\\Facades\\UIManager::isInActivePath({$path})): echo e({$class}); endif; ?>";
        });
        array_map(function($type){
            Blade::directive($type, function($expression) use ($type){
                return  '<?php if(Auth::user()->is'. ucfirst($type) . '()): ?>';
            });
            Blade::directive('end' . $type, function(){ return '<?php endif; ?>'; });
        }, ['admin', 'client', 'supplier']);
        CustomUrlHandler::add('\\App\\Http\\Controllers\\KnowledgeBaseController@displayPostCategory', __('Post Category Handler'))
            ->add('\\App\\Http\\Controllers\\KnowledgeBaseController@displayPost', 'Post Handler');

        Blade::directive('someError', function($expression){
            return "<?php if(array_reduce({$expression}, function(\$hasError, \$name) use (\$errors){return \$hasError || \$errors->has(\$name);}, false)): ?>";
        });
        Blade::directive('endSomeError', function($expression){
            return '<?php endif; ?>';
        });

    }
}
