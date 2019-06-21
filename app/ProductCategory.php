<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;

class ProductCategory extends Model
{
    const CATEGORY_BOX = 'box';
    const CATEGORY_BOX_SERVICE = 'box-service';
    protected $casts = ['active' => 'boolean', 'custom_attributes'=>'array'];
    protected $fillable = ['code', 'name', 'custom_attributes'];
    protected $table = 'product_categories';


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
}
