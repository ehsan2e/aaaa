<?php

namespace App;

use App\Facades\CustomUrlHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use function NovaVoip\supervisedTransaction;


/**
 * @property mixed url
 */
class CustomUrl extends Model
{
    const REDIRECT_STATUS_CODES = [
        '301' => '301 Moved Permanently',
        '302' => '302 Found',
    ];
    protected $appends = ['url'];
    protected $casts = [
        'parameters' => 'array',
    ];
    protected $table = 'custom_urls';

    public function getHandlerTypeAttribute()
    {
        if(!isset($this->handler)){
            throw new \RuntimeException('handler is not specified');
        }
        return CustomUrlHandler::getHandlers()[$this->handler] ?? '?';
    }

    public function getTargetUrlAttribute()
    {
        if(!isset($this->redirect_url)){
            throw new \RuntimeException('redirect_url is not specified');
        }
        return url($this->redirect_url);
    }

    public function getUrlAttribute(): string
    {
        return url($this->path);
    }

    /**
     * @param $data
     * @return bool
     */
    public function updateInfo($data): bool
    {
        $this->path = $data['path'];
        if (isset($data['redirect_url'])) {
            $this->redirect_url = $data['redirect_url'];
            $this->redirect_status = $data['redirect_status'] ?? '301';
        }else{
            $this->handler = $data['handler'];
            $this->parameters = isset($data['parameters'])? (is_scalar($data['parameters']) ? json_decode($data['parameters']) : data['parameters']) : null;
            $this->redirect_status = null;
        }
        return $this->save();
    }

    /**
     * @param array $data
     * @return CustomUrl|null
     */
    public static function createNewCustomUrl(array $data): ?CustomUrl
    {
            $instance = new self;
            return $instance->updateInfo($data)? $instance : null;
    }
}
