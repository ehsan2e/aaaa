<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ability extends Model
{
    const ALL = 'all';
    const BACKEND_ADMIN = 'backend-admin';
    const COMPOSE_HTML = 'compose-html';
    const LOGIN_AS = 'login-as';
    const LOGIN_AS_CLIENT = 'login-as-client';
    const LOGIN_AS_SUPPLIER = 'login-as-supplier';
    const MODEL_ACCESS_ALL = 'model_access_all';
    const RAW_QUERY = 'raw-query';

    protected $fillable = ['*'];
    protected $table = 'abilities';

    protected static $cache=[];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_abilities', 'ability_id', 'role_id', 'id', 'id', 'roles');
    }

    public static function getAbilities()
    {
        return [
            self::ALL => [
                __('All'),
                [],
            ],
            self::BACKEND_ADMIN => [
                __('Admin of backend'),
                [self::ALL],
            ],
            self::COMPOSE_HTML => [
                __('Use html tags in texts'),
                [],
            ],
            self::LOGIN_AS => [
                __('Login as either client or supplier'),
                [self::BACKEND_ADMIN],
            ],
            self::LOGIN_AS_CLIENT => [
                __('Login as clients'),
                [self::LOGIN_AS],
            ],
            self::LOGIN_AS_SUPPLIER => [
                __('Login as suppliers'),
                [self::LOGIN_AS],
            ],
            self::MODEL_ACCESS_ALL => [
                __('Allowed to modify any fillable fields and relations of the model'),
                [self::BACKEND_ADMIN],
            ],
            self::RAW_QUERY => [
                __('Run raw query in listing search boxes'),
                [self::ALL],
            ],
        ];
    }


    public static function getParentAbilities(string $ability, bool $includeSelf = false): array
    {
        $cacheKey = $ability . $includeSelf ? '-yes' : '-no';
        if(!isset(self::$cache[$cacheKey])){
            $abilities = self::getAbilities();
            self::$cache[$cacheKey] = $includeSelf ? [$ability] : [];
            $closedList = [$ability];
            $openList = $abilities[$ability][1] ?? [];
            while(count($openList) > 0){
                $currentAbility = array_shift($openList);
                if(isset($closedList[$currentAbility])){
                    continue;
                }
                self::$cache[$cacheKey][] = $currentAbility;
                $closedList[$currentAbility] = true;
                $openList = array_merge($openList, $abilities[$currentAbility][1] ?? []);
            }
        }
        return self::$cache[$cacheKey];
    }
}
