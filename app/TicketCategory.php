<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    const TYPE_INCOMING = 1;
    const TYPE_INTERNAL = 2;
    const TYPE_OUTGOING = 3;

    const TYPES = [
        self::TYPE_INCOMING,
        self::TYPE_INTERNAL,
        self::TYPE_OUTGOING,
    ];
    protected $appends = ['type_caption'];
    protected $casts = [
        'active' => 'boolean',
        'title_translations' => 'array',
    ];
    protected $fillable = ['active', 'title', 'title_translations', 'type'];
    protected $table = 'ticket_categories';

    /**
     * @var array|null
     */
    protected static $typeCaptions;

    public function getTypeCaptionAttribute(): string {
        return self::getTypes()[$this->type] ?? $this->type;
    }
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id', 'id');
    }

    public function updateInfo($data): bool
    {
        $instance = $this;
        $instance->fill($data);
        $instance->active = isset($data['active']);
        return $instance->save();
    }

    public static function createNewTicketCategory(array $data): ?TicketCategory
    {
        $instance = new self($data);
        $instance->active = isset($data['active']);
        return $instance->save() ? $instance : null;
    }

    public static function getTypes(): array {
        if(!self::$typeCaptions){
            self::$typeCaptions = [
                self::TYPE_INCOMING => __('Incoming'),
                self::TYPE_INTERNAL => __('Internal'),
                self::TYPE_OUTGOING => __('Outgoing'),
            ];
        }
        return self::$typeCaptions;
    }
}
