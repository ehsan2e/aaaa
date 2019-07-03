<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxGroup extends Model
{
    protected $casts = ['active' => 'boolean', 'is_percentage' => 'boolean', 'name_translations' => 'array'];
    protected $fillable = ['active', 'amount', 'is_percentage', 'name', 'name_translations'];
    protected $table = 'tax_groups';

    public function productTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProductType::class, 'product_type_tax_groups', 'tax_group_id', 'product_type_id', 'id', 'id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(TaxRule::class, 'tax_group_id', 'id');
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @param User $user
     * @param array $data
     * @return TaxGroup|null
     */
    public static function createNewTaxGroup(User $user, array $data): ?TaxGroup
    {
        $instance = new self($data);
        $instance->active = isset($data['active']);
        $instance->is_percentage = isset($data['is_percentage']);
        $instance->user()->associate($user);
        return $instance->save() ? $instance : null;
    }

    /**
     * @param User $user
     * @param array $data
     * @return TaxRule|null
     */
    public function defineNewTaxRule(User $user, array $data): ?TaxRule
    {
        return TaxRule::createNewTaxRule($this, $user, $data);
    }
}
