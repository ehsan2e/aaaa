<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $casts = ['active' => 'boolean', 'name_translations' => 'array'];
    protected $fillable = ['code', 'name', 'names', 'active'];
    protected $keyType = 'string';
    protected $primaryKey = 'code';
    protected $table = 'countries';
    public $timestamps = false;

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class, 'country_code', 'code');
    }
}
