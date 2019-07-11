<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;
use NovaVoip\Traits\MaskId;

class Ticket extends Model
{
    use MaskId;

    const MASK_MULTIPLIER = 5073;
    const MASK_NAME = 'ticket_number';
    const MASK_OFFSET = 109;
    const MASK_PREFIX = 'TCK-';

    const STATUS_NEEDS_ACTION = 1;
    const STATUS_WAITING_RESPONSE = 2;
    const STATUS_IN_PROGRESS = 3;

    const STATUSES = [
        self::STATUS_NEEDS_ACTION,
        self::STATUS_WAITING_RESPONSE,
        self::STATUS_IN_PROGRESS,
    ];

    const URGENCY_LOW = 1;
    const URGENCY_NORMAL = 2;
    const URGENCY_HIGH = 3;
    const URGENCY_URGENT = 4;

    const URGENCY = [
        self::URGENCY_LOW,
        self::URGENCY_NORMAL,
        self::URGENCY_HIGH,
        self::URGENCY_URGENT,
    ];

    protected $appends = ['ticket_number'];
    protected $casts = [
        'ticket_reference' => 'boolean',
        'reopened' => 'boolean',
        'reopen_allowed' => 'boolean',
    ];
    protected $dates = ['last_response_at', 'last_interaction_at', 'progress_date'];
    protected $fillable = ['subject', 'urgency'];
    protected $table = 'tickets';

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id', 'id');
    }

    public function getTicketNumberAttribute()
    {
        return self::generateMask($this->id);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id', 'id');
    }

    public function lastInteractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_interactor', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ticketReference(): MorphTo
    {
        return $this->morphTo('ticket_reference');
    }

    /**
     * @param User $user
     * @param array $data
     * @param $insight
     * @return Supplier|null
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public static function createNewIncomingTicket(User $user, array $data, &$insight): ?Ticket
    {
        return supervisedTransaction(function ($insight) use ($data, $user): ?Ticket {
            /** @var TicketCategory|null $category */
            $category = TicketCategory::where('type', TicketCategory::TYPE_INCOMING)->where('active', true)->sharedLock()->find($data['category_id']);
            $now = Carbon::now();
            if (!$category) {
                return null;
            }
            $instance = new static($data);
            $instance->progress_date = $now;
            $instance->last_response_at = $now;
            $instance->user()->associate($user);
            $instance->initiator()->associate($user);
            $instance->status = self::STATUS_NEEDS_ACTION;
            $instance->category()->associate($category);
            if (!$instance->save()) {
                return null;
            }

            $ticketEntry = new TicketEntry($data);
            $ticketEntry->type = TicketEntry::TYPE_INCOMING;
            $ticketEntry->is_html = $user->can(Ability::COMPOSE_HTML);
            $ticketEntry->visible_on_front = true;
            $ticketEntry->ticket()->associate($instance);
            $ticketEntry->user()->associate($user);
            if (!$ticketEntry->save()) {
                throw new SupervisedTransactionException('Could not create ticket entry');
            }

            $ticketHistory = new TicketHistory(['what' => TicketHistory::WHAT_CREATED]);
            $ticketHistory->ticket()->associate($instance);
            $ticketHistory->user()->associate($user);

            if (!$ticketHistory->save()) {
                throw new SupervisedTransactionException('Could not create ticket history');
            }

            if (!(TicketFollower::register($instance, $user, TicketFollower::CONCERN_OWNER, TicketFollower::CONCERN_INITIATOR))) {
                throw new SupervisedTransactionException('Could not create ticket followers');
            }

            if (isset($data['attachments'])) {
                Attachment::claim($instance, $data['attachments']);
            }

            return $instance;
        }, null, false, false, $insight);
    }

    public static function getUrgencies(): array
    {
        return [
            self::URGENCY_LOW => __('Low'),
            self::URGENCY_NORMAL => __('Normal'),
            self::URGENCY_HIGH => __('High'),
            self::URGENCY_URGENT => __('Urgent'),
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEEDS_ACTION => _('Needs Action'),
            self::STATUS_WAITING_RESPONSE => _('Waiting For Response'),
            self::STATUS_IN_PROGRESS => _('In Progress'),
        ];
    }
}
