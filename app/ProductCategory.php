<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;
use function NovaVoip\translateEntity;

class ProductCategory extends Model
{
    const CATEGORY_BOX = 'box';
    const CATEGORY_BOX_SERVICE = 'box-service';
    protected $casts = ['active' => 'boolean', 'custom_attributes' => 'array'];
    protected $fillable = ['code', 'name', 'custom_attributes'];
    protected $table = 'product_categories';

    /**
     * @param bool $isBackend
     * @param string $prefix
     * @return array
     */
    public function getCustomAttributeCaptions(bool $isBackend = false, string $prefix = 'custom_attributes.'): array
    {
        $captions = [];
        foreach ($this->custom_attributes as $customAttribute){
            $captions[$prefix . $customAttribute['name']] = translateEntity($customAttribute, 'caption', 'captions', $isBackend);
        }
        return $captions;
    }

    /**
     * @param string $prefix
     * @return array
     */
    function getCustomAttributeValidationRules(string $prefix = 'custom_attributes.'): array
    {
        if (!isset($this->custom_attributes)) {
            return [];
        }
        $rules = [];
        foreach ($this->custom_attributes as $customAttribute) {
            $r = [];
            if ($customAttribute['required']) {
                $r[] = 'required';
            } else {
                $r[] = 'nullable';
            }
            switch ($customAttribute['type']){
                case'price':
                case'decimal':
                    $r[] = 'numeric';
                    break;
                case'integer':
                    $r[] = 'integer';
                    break;
                case 'lookup':
                    $r[] = 'in:' . array_reduce($customAttribute['lookupValues'] ?? [], function($values, $lookupValue){ return $values . ($values === '' ? '' : ',') . $lookupValue['value'];}, '');
                    break;
            }
            $rules[$prefix . $customAttribute['name']] = $r;
        }
        return $rules;

    }

    /**
     * @return stirng
     */
    public function getChildrenAddress(): string
    {
        return (isset($this->address) ? $this->address : '-') . $this->id . '-';
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductType::class, 'category_id', 'id');
    }


    /**
     * @param array $data
     * @param null $insight
     * @return bool
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public function updateInfo(array $data, &$insight): bool
    {
        return supervisedTransaction(function ($insight) use ($data): bool {
            /** @var Collection $collection */
            $collection = self::lockForUpdate()->where('id', $this->id)->orWhere('parent_id', $this->id)->get();
            if (count($collection) > 1) {
                /** @var PostCategory $instance */
                $instance = $collection->first(function ($item) {
                    return $item->id == $this->id;
                });
                $hasChildren = true;
            } else {
                /** @var PostCategory $instance */
                $instance = $collection->first();
                $hasChildren = false;
            }
            if (
                $hasChildren &&
                (
                    (!isset($data['parent_id']) && isset($instance->parent_id))
                    || (isset($data['parent_id']) && ($instance->parent_id != $data['parent_id']))
                )
            ) {
                $insight->message = __('Cannot change the parent of a category which already has children');
                return false;
            }

            $instance->fill($data);
            $instance->active = isset($data['active']);
            if (isset($data['parent_id'])) {
                /** @var ProductCategory $parent */
                $parent = self::lockForUpdate()->find($data['parent_id']);
                if (!$parent) {
                    throw new SupervisedTransactionException('Parent does not exist');
                }
                $instance->address = $parent->getChildrenAddress();
                $instance->parentCategory()->associate($parent);
            } else {
                $instance->address = null;
                $instance->parent_id = null;
            }

            return $instance->save();
        }, false, false, false, $insight);
    }

    /**
     * @param array $data
     * @param $insight
     * @return PostCategory|null
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public static function createNewCategory(array $data, &$insight): ?ProductCategory
    {
        return supervisedTransaction(function ($insight) use ($data): ?ProductCategory {
            $instance = new self($data);
            $instance->active = isset($data['active']);
            if (isset($data['parent_id'])) {
                /** @var ProductCategory $parent */
                $parent = self::lockForUpdate()->find($data['parent_id']);
                if (!$parent) {
                    throw new SupervisedTransactionException('Parent does not exist');
                }
                $instance->address = $parent->getChildrenAddress();
                $instance->parentCategory()->associate($parent);
            } else {
                $instance->address = null;
                $instance->parent_id = null;
            }
            if (!$instance->save()) {
                throw new SupervisedTransactionException('Could not create post category');
            }
            return $instance;
        }, null, false, false, $insight);
    }

    /**
     * @return array
     */
    public static function getAttributeTypes(): array
    {
        return [
            'boolean' => 'Boolean',
            'decimal' => 'Decimal',
            'html' => 'Html',
            'integer' => 'Integer',
            'lookup' => 'Lookup',
            'picture' => 'Picture',
            'price' => 'Price',
            'string' => 'String',
            'text' => 'Text',
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function parseCustomAttributes(array $data): array
    {
        $result = [];
        foreach ($this->custom_attributes as $customAttribute){
            if(!array_key_exists($customAttribute['name'], $data)){
                if($customAttribute['type'] === 'boolean'){
                    $result[$customAttribute['name']] = false;
                }elseif (isset($customAttribute['default'])){
                    $result[$customAttribute['name']] = $customAttribute['default'];
                }
                continue;
            }
            if((!isset($data[$customAttribute['name']])) && in_array($customAttribute['type'], ['price', 'decimal', 'integer'])){
                $result[$customAttribute['name']] = null;
                continue;
            }
            switch($customAttribute['type']){
                case'price':
                case'decimal':
                    $result[$customAttribute['name']] = (float) $data[$customAttribute['name']];
                    break;
                case'integer':
                    $result[$customAttribute['name']] = (int) $data[$customAttribute['name']];
                    break;
                case 'boolean':
                    $result[$customAttribute['name']] = (bool) $data[$customAttribute['name']];
                    break;
                default:
                    $result[$customAttribute['name']] = (string) $data[$customAttribute['name']];
            }
        }

        return $result;
    }
}
