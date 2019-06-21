<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class CustomUrlHandler
 * @package App\Facades
 *
 * @method static \NovaVoip\Helpers\CustomUrlHandler add(string $handler, string $label)
 * @method static array getHandlers()
 */
class CustomUrlHandler extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'custom-url-handler';
    }
}