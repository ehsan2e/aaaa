<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use NovaVoip\Exceptions\SupervisedTransactionException;
use NovaVoip\Interfaces\iTransaction;
use NovaVoip\Interfaces\iTransactionCollectionGenerator;
use function NovaVoip\supervisedTransaction;

class Transaction extends Model
{
    const TYPE_BANK_TRANSFER = 1;
    const FAILURE_NO_TRANSACTION = 1;
    const FAILURE_NEGATIVE_BALANCE = 2;

    protected $fillable = ['old_balance','amount','new_balance','type','description'];
    protected $table = 'transactions';

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator', 'id');
    }

    public function reason(): MorphTo
    {
        return $this->morphTo('reason', 'reason_type', 'reason_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @param User $user
     * @param iTransactionCollectionGenerator $transactionCollectionGenerator
     * @param callable|null $fn
     * @param null $insight
     * @return bool
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public static function createTransaction(User $user, iTransactionCollectionGenerator $transactionCollectionGenerator, callable $fn = null, &$insight=null): bool
    {
        return supervisedTransaction(function($insight) use ($user, $transactionCollectionGenerator, $fn){
            /** @var User $lockedUser */
            $lockedUser = User::query()->lockForUpdate()->find($user->id);
            $availableBalance = $user->balance;
            $transactionCollection = $transactionCollectionGenerator->generate($availableBalance);
            if(is_null($transactionCollection)){
                $insight->failure = self::FAILURE_NO_TRANSACTION;
                return false;
            }

            $transactions = [];
            /** @var iTransaction $transaction */
            foreach ($transactionCollection->transactions() as $transaction)
            {
                $instance = new self();
                $instance->old_balance = $availableBalance;
                $instance->amount = $transaction->amount();
                $availableBalance += $transaction->amount();
                if($availableBalance < 0){
                    $insight->failure = self::FAILURE_NEGATIVE_BALANCE;
                    throw new SupervisedTransactionException('Negative balance is not allowed');
                }
                $instance->new_balance = $availableBalance;
                $instance->type = $transaction->type();
                $instance->description = $transaction->description();
                /** @var Model|nul $reason */
                $reason = $transaction->reason();
                if(is_null($reason)){
                    $instance->reason_type = null;
                    $instance->reason_id = null;
                }else{
                    $instance->reason()->associate($reason);
                }

                if(($initiator = $transaction->initiator()) !== null){
                    $instance->initiator()->associate($initiator);
                }

                if(!$user->transactions()->save($instance)){
                    throw new SupervisedTransactionException('Could not create transaction');
                }
                $transactions[] = $instance;
            }

            $user->balance = $availableBalance;
            if(!$user->save()){
                throw new SupervisedTransactionException('Could not update user balance');
            }

            if(is_callable($fn) && (!$fn($transactions, $insight))){
                throw new SupervisedTransactionException('Error in callback');
            }

            return true;
        }, false, true, false, $insight);
    }
}
