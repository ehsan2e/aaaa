<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleAbility extends Pivot
{
    protected $table = 'role_abilities';
}
