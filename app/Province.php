<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Province extends Model
{
    protected $casts = ['active' => 'boolean', 'name_translations' => 'array'];
    protected $fillable = ['code', 'country_code', 'name', 'names', 'active'];
    protected $keyType = 'string';
    protected $primaryKey = 'code';
    protected $table = 'provinces';
    public $timestamps = false;

    public function country(): BelongsTo
    {
        return $this->belongs(Country::class, 'country_code', 'code');
    }
}
