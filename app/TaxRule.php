<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRule extends Model
{
    protected $casts = ['active' => 'boolean', 'is_percentage' => 'boolean', 'extra_information' => 'array'];
    protected $fillable = ['active', 'amount', 'country_code', 'extra_information', 'is_percentage', 'priority', 'province_code'];
    protected $table = 'tax_rules';

    public function country(): BelongsTo
    {
        return $this->belongs(Country::class, 'country_code', 'code');
    }

    public function province(): BelongsTo
    {
        return $this->belongs(Province::class, 'province_code', 'code');
    }

    public function taxGroup(): BelongsTo
    {
        return $this->belongsTo(TaxGroup::class, 'tax_group_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function updateInfo(User $user, array $data): bool
    {
        $instance = $this;
        $instance->fill($data);
        $instance->active = isset($data['active']);
        $instance->is_percentage = isset($data['is_percentage']);
        $instance->user()->associate($user);
        return $instance->save();
    }

    /**
     * @param TaxGroup $taxGroup
     * @param User $user
     * @param array $data
     * @return TaxRule|null
     */
    public static function createNewTaxRule(TaxGroup $taxGroup, User $user, array $data): ?TaxRule
    {
        $instance = new self($data);
        $instance->taxGroup()->associate($taxGroup);
        $instance->active = isset($data['active']);
        $instance->is_percentage = isset($data['is_percentage']);
        $instance->user()->associate($user);
        return $instance->save() ? $instance : null;
    }
}
