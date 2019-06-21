<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;
use NovaVoip\Traits\SeoAble;

class Post extends Model
{
    use SeoAble;
    protected $appends = ['permalink'];
    protected $casts = ['active' => 'boolean', 'disclose_author' => 'boolean'];
    protected $fillable = ['code', 'language', 'title', 'picture', 'excerpt', 'content'];
    protected $table = 'posts';

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id', 'id');
    }

    public function getPermalinkAttribute(): string
    {
        if ($this->url) {
            return url($this->url->path);
        }
        return route('knowledge-base.post', ['post' => $this]);
    }

    /**
     * @param User|null $author
     * @param array $data
     * @param null $insight
     * @return bool
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public function updateInfo(?User $author, array $data, &$insight): bool
    {
        return supervisedTransaction(function ($insight) use ($author, $data): bool {
            /** @var Post $instance */
            $instance = self::lockForUpdate()->find($this->id);
            if (!$instance->seoConfig->update($data)) {
                return false;
            }
            if (isset($author)) {
                $instance->author()->associate($author);
            } else {
                $instance->author_id = null;
            }
            $instance->fill($data);
            $instance->active = isset($data['active']);
            $instance->disclose_author = isset($data['disclose_author']);
            if (isset($data['category_id'])) {
                /** @var PostCategory $category */
                $category = PostCategory::lockForUpdate()->find($data['category_id']);
                if (!$category) {
                    throw new SupervisedTransactionException('Category does not exist');
                }
                if ($category->language !== $data['language']) {
                    throw new SupervisedTransactionException('Category is not from the same language');
                }
                $instance->category()->associate($category);
            } else {
                $instance->category_id = null;
            }

            switch($instance->updateCustomUrl(
                $data['seo_link'] ?? null,
                '\\App\\Http\\Controllers\\KnowledgeBaseController@displayPost',
                [
                    'id' => $instance->id
                ]
            )){
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
                    throw new SupervisedTransactionException('Could not update post');

            }

            return true;
        }, false, false, false, $insight);
    }

    public function url(): BelongsTo
    {
        return $this->belongsTo(CustomUrl::class, 'url_id', 'id');
    }

    /**
     * @param User $author
     * @param array $data
     * @param null $insight
     * @return Post|null
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public static function createNewPost(?User $author, array $data, &$insight): ?Post
    {
        return supervisedTransaction(function ($insight) use ($author, $data): ?Post {
            $seoConfig = new SeoConfig($data);
            if (!$seoConfig->save()) {
                return null;
            }
            $instance = new self($data);
            if (isset($author)) {
                $instance->author()->associate($author);
            } else {
                $instance->author_id = null;
            }
            $instance->active = isset($data['active']);
            $instance->disclose_author = isset($data['disclose_author']);
            if (isset($data['category_id'])) {
                /** @var PostCategory $category */
                $category = PostCategory::lockForUpdate()->find($data['category_id']);
                if (!$category) {
                    throw new SupervisedTransactionException('Category does not exist');
                }
                if ($category->language !== $data['language']) {
                    throw new SupervisedTransactionException('Category is not from the same language');
                }
                $instance->category()->associate($category);
            } else {
                $instance->category_id = null;
            }
            $instance->seoConfig()->associate($seoConfig);
            if (!$instance->save()) {
                throw new SupervisedTransactionException('Could not create post');
            }
            switch($instance->updateCustomUrl(
                $data['seo_link'] ?? null,
                '\\App\\Http\\Controllers\\KnowledgeBaseController@displayPost',
                [
                    'id' => $instance->id
                ]
            )){
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
                    throw new SupervisedTransactionException('Could not create post');

            }

            return $instance;
        }, null, false, false, $insight);
    }
}
