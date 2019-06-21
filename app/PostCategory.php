<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;
use NovaVoip\Traits\SeoAble;

/**
 * @property mixed permalink
 */
class PostCategory extends Model
{
    use SeoAble;
    protected $appends = ['permalink'];
    protected $casts = ['active' => 'boolean'];
    protected $fillable = ['code', 'language', 'name', 'picture'];
    protected $table = 'post_categories';

    /**
     * @return stirng
     */
    public function getChildrenAddress(): string
    {
        return (isset($this->address) ? $this->address : '-') . $this->id . '-';
    }

    public function getPermalinkAttribute(): string
    {
        if ($this->url) {
            return url($this->url->path);
        }

        return route('knowledge-base.category', ['category' => $this]);
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
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

            if ($hasChildren && ($data['language'] !== $instance->language)) {
                $insight->message = __('Cannot change the language of a category which already has children');
                return false;
            }
            if (!$instance->seoConfig->update($data)) {
                return false;
            }
            $instance->fill($data);
            $instance->active = isset($data['active']);
            if (isset($data['parent_id'])) {
                /** @var PostCategory $parent */
                $parent = self::lockForUpdate()->find($data['parent_id']);
                if (!$parent) {
                    throw new SupervisedTransactionException('Parent does not exist');
                }
                if ($parent->language !== $data['language']) {
                    throw new SupervisedTransactionException('Parent is not from the same language');
                }
                $instance->address = $parent->getChildrenAddress();
                $instance->parentCategory()->associate($parent);
            } else {
                $instance->address = null;
                $instance->parent_id = null;
            }

            switch ($result = $instance->updateCustomUrl(
                $data['seo_link'] ?? null,
                '\\App\\Http\\Controllers\\KnowledgeBaseController@displayPostCategory',
                [
                    'id' => $instance->id
                ]
            )) {
                case 0:
                    // we are ok;
                    break;
                case 1:
                    throw new SupervisedTransactionException('Could not create custom url');
                    break;
                case 2:
                    $insight = __('Similar url already defined');
                    throw new SupervisedTransactionException();
                    break;
                case 3:
                    throw new SupervisedTransactionException('Could not update post category');

            }

            return true;
        }, false, false, false, $insight);
    }

    public function url(): BelongsTo
    {
        return $this->belongsTo(CustomUrl::class, 'url_id', 'id');
    }

    /**
     * @param array $data
     * @param $insight
     * @return PostCategory|null
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public static function createNewCategory(array $data, &$insight): ?PostCategory
    {
        return supervisedTransaction(function ($insight) use ($data): ?PostCategory {
            $seoConfig = new SeoConfig($data);
            if (!$seoConfig->save()) {
                return null;
            }
            $instance = new self($data);
            $instance->active = isset($data['active']);
            if (isset($data['parent_id'])) {
                /** @var PostCategory $parent */
                $parent = self::lockForUpdate()->find($data['parent_id']);
                if (!$parent) {
                    throw new SupervisedTransactionException('Parent does not exist');
                }
                if ($parent->language !== $data['language']) {
                    throw new SupervisedTransactionException('Parent is not from the same language');
                }
                $instance->address = $parent->getChildrenAddress();
                $instance->parentCategory()->associate($parent);
            } else {
                $instance->address = null;
                $instance->parent_id = null;
            }
            $instance->seoConfig()->associate($seoConfig);
            if (!$instance->save()) {
                throw new SupervisedTransactionException('Could not create post category');
            }
            switch ($result = $instance->updateCustomUrl(
                $data['seo_link'] ?? null,
                '\\App\\Http\\Controllers\\KnowledgeBaseController@displayPostCategory',
                [
                    'id' => $instance->id
                ]
            )) {
                case 0:
                    // we are ok;
                    break;
                case 1:
                    throw new SupervisedTransactionException('Could not create custom url');
                    break;
                case 2:
                    $insight = __('Similar url already defined');
                    throw new SupervisedTransactionException();
                    break;
                case 3:
                    throw new SupervisedTransactionException('Could not create post category');

            }

            return $instance;
        }, null, false, false, $insight);
    }

}
