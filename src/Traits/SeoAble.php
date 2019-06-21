<?php

namespace NovaVoip\Traits;


use App\CustomUrl;
use App\SeoConfig;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;

trait SeoAble
{
    public function seoConfig(): BelongsTo
    {
        return $this->belongsTo(SeoConfig::class, 'seo_config_id', 'id');
    }

    /**
     * @param null|string $path
     * @param string $handler
     * @param array $parameters
     * @param bool $persist
     * @return int
     * @throws \Exception
     */
    protected function updateCustomUrl(?string $path, string $handler, array $parameters = [], bool $persist = true): int
    {
        /** @var CustomUrl $originalUrl */
        $originalUrl = isset($this->url_id) ? $this->url : null;
        if (isset($path) && ((!isset($originalUrl)) || ($originalUrl->path != $path))) {
            $customUrl = new CustomUrl();
            $customUrl->path = $path;
            $customUrl->handler = $handler;
            $customUrl->parameters = $parameters;
            $customUrl->redirect_status = null;
            try {
                if (!$customUrl->save()) {
                    return 1;
                }
            } catch (QueryException $qex) {
                return 2;
            }
            $this->url()->associate($customUrl);
        } elseif (isset($path, $originalUrl) && ($originalUrl->path == $path)) {
            // we do nothing
        } else {
            $this->url_id = null;
        }

        if ($persist) {
            if (!$this->save()) {
                return 3;
            }

            if (isset($originalUrl) && ($path != $originalUrl->path)) {
                $originalUrl->delete();
            }
        }
        return 0;
    }
}