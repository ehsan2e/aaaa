<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketHistory extends Model
{
    const WHAT_CREATED = 1;
    const WHAT_SET_ASSIGNEE = 2;
    const WHAT_EDITED_SUBJECT = 3;
    const WHAT_EDITED_MESSAGE = 4;
    const WHAT_CLOSED = 5;
    const WHAT_CLOSED_FOREVER = 6;
    const WHAT_REOPENED = 7;

    const WHATS = [
        self::WHAT_CREATED,
        self::WHAT_SET_ASSIGNEE,
        self::WHAT_EDITED_SUBJECT,
        self::WHAT_EDITED_MESSAGE,
        self::WHAT_CLOSED,
        self::WHAT_CLOSED_FOREVER,
        self::WHAT_REOPENED,
    ];

    protected $fillable = ['from', 'to', 'what'];
    protected $table = 'ticket_histories';

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function getWhats(): array
    {
        return [
            self::WHAT_CREATED => _('Created'),
            self::WHAT_SET_ASSIGNEE => _('Set Assignee'),
            self::WHAT_EDITED_SUBJECT => _('Edited Subject'),
            self::WHAT_EDITED_MESSAGE => _('Edited Message'),
            self::WHAT_CLOSED => _('Closed'),
            self::WHAT_CLOSED_FOREVER => _('Closed Forever'),
            self::WHAT_REOPENED => _('Reopened'),
        ];
    }
}
