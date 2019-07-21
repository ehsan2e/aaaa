<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NovaVoip\Helpers\PaymentGatewayResolver;
use NovaVoip\Interfaces\iPaymentGateway;
use NovaVoip\Traits\MaskId;

class Payment extends Model
{
    use MaskId;

    const MASK_MULTIPLIER = 4013;
    const MASK_NAME = 'payment_number';
    const MASK_OFFSET = 524;
    const MASK_PREFIX = 'P-';

    const STATUS_NEW = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_VERIFYING = 3;
    const STATUS_SUCCEED = 4;
    const STATUS_FAILED = 5;
    const STATUS_REJECTED = 6;

    protected $appends = [self::MASK_NAME, 'status_caption'];
    protected $casts = ['information' => 'array', 'process_data' => 'array'];
    protected $fillable = ['amount', 'gateway', 'information', 'process_data', 'status', 'unique_filed', 'reference_number'];
    protected $table = 'payments';

    public function getPaymentNumberAttribute()
    {
        return self::generateMask($this->id);
    }

    public function getStatusCaptionAttribute()
    {
        return self::getStatuses()[$this->status] ?? __('Unknown (:status)', ['status' => $this->status]);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function verify(array $requestData = []): bool
    {
        /** @var iPaymentGateway $gateway */
        $gateway = app(PaymentGatewayResolver::class)->resolve($this->gateway);
        if (!$gateway) {
            return false;
        }

        $result = $gateway->verify($this, $requestData);

        if ($result) {
            $this->reference_number = $gateway->referenceNumber();
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => __('New'),
            self::STATUS_IN_PROGRESS => __('In Progress'),
            self::STATUS_VERIFYING => __('Verifying'),
            self::STATUS_SUCCEED => __('Succeed'),
            self::STATUS_FAILED => __('Failed'),
            self::STATUS_REJECTED => __('Rejected'),
        ];
    }
}
