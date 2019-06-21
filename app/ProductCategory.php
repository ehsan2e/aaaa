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
