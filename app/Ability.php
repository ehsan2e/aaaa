<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ability extends Model
{
    protected $table = 'abilities';


    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_abilities', 'ability_id', 'role_id', 'id', 'id', 'roles');
    }
}
