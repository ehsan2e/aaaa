<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class CustomUrlHandler
 * @package App\Facades
 *
 * @method static \NovaVoip\Helpers\UIManager addToActivePath(string $path)
 * @method static array getActivePath()
 * @method static bool isInActivePath(string|array $path)
 * @method static array prepareMenu(array $menuConfig)
 * @method static \NovaVoip\Helpers\UIManager setActivePath(string[] ...$activePath)
 */
class UIManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ui-manager';
    }
}