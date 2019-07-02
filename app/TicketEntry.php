<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TicketEntry extends Model
{
    const TYPE_INCOMING = 1;
    const TYPE_OUTGOING = 2;
    const TYPE_INTERNAL = 3;

    const TYPES = [
        self::TYPE_INCOMING,
        self::TYPE_OUTGOING,
        self::TYPE_INTERNAL,
    ];

    protected $casts = ['is_html' => 'boolean', 'visible_on_front' => 'boolean'];
    protected $fillable = ['message'];
    protected $table = 'ticket_entries';

    public static function getTypes(): array
    {
        return [
            self::TYPE_INCOMING => __('Incoming'),
            self::TYPE_OUTGOING => __('Outgoing'),
            self::TYPE_INTERNAL => __('Internal'),
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee', 'id');
    }

    public function attachments(): MorphMany
    {
        $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
