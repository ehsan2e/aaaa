<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

class TicketFollower extends Pivot
{
    const CONCERN_INFORMANT = 1;
    const CONCERN_OWNER = 2;
    const CONCERN_INITIATOR = 4;
    const CONCERN_INTRACTOR = 8;
    const CONCERN_ASSIGNEE = 16;

    const CONCERNS = [
        self::CONCERN_INFORMANT,
        self::CONCERN_OWNER,
        self::CONCERN_INITIATOR,
        self::CONCERN_INTRACTOR,
        self::CONCERN_ASSIGNEE,
    ];

    protected $appends = ['ticket_number'];
    protected $casts = [
        'following' => 'boolean',
    ];
    protected $fillable = ['concern', 'following', 'interactions', 'updated_at'];
    protected $table = 'ticket_followers';
    public $timestamps = false;


    public function getTicketNumberAttribute()
    {
        return Ticket::generateTicketNumber($this->ticket_id);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function getConcernsList(): array
    {
        return [
            self::CONCERN_INFORMANT => __('Informant'),
            self::CONCERN_OWNER => __('Owner'),
            self::CONCERN_INITIATOR => __('Initiator'),
            self::CONCERN_INTRACTOR => __('Intractor'),
            self::CONCERN_ASSIGNEE => __('Assignee'),
        ];
    }

    public static function register(Ticket $ticket, User $user, int ...$concerns): bool
    {
        $now = Carbon::now();
        $interactions = count($concerns);
        $combinedConcern = array_reduce($concerns, function ($combinedConcern, $concern) {
            return $combinedConcern | $concern;
        }, 0);
        /** @todo what about postgres */
        $sql = <<<'SQL'
INSERT INTO `ticket_followers`
(`ticket_id`, `user_id`, `concern`, `interactions`, `following`, `updated_at`)
VALUES ('%d', '%d', '%d', '%d', '1', '%s')
ON DUPLICATE KEY UPDATE
   `concern` = `concern` | %3$d,
   `interactions` = `interactions` + %4$d,
   `following` = '1',
   `updated_at` = '%5$s';
SQL;
        return DB::unprepared(sprintf($sql, $ticket->id, $user->id, $combinedConcern, $interactions, $now));
    }
}
