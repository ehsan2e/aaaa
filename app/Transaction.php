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
     * @param null $insight
     * @return bool
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public static function createTransaction(User $user, iTransactionCollectionGenerator $transactionCollectionGenerator, &$insight=null): bool
    {
        return supervisedTransaction(function($insight) use ($user, $transactionCollectionGenerator){
            /** @var User $lockedUser */
            $lockedUser = User::query()->lockForUpdate()->find($user->id);
            $availableBalance = $user->balance;
            $transactionCollection = $transactionCollectionGenerator->generate($availableBalance);
            if(is_null($transactionCollection)){
                return false;
            }

            /** @var iTransaction $transaction */
            foreach ($transactionCollection->transactions() as $transaction)
            {
                $instance = new self();
                $instance->old_balance = $availableBalance;
                $instance->amount = $transaction->amount();
                $availableBalance += $transaction->amount();
                if($availableBalance < 0){
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
            }

            $user->balance = $availableBalance;
            if(!$user->save()){
                throw new SupervisedTransactionException('Could not update user balance');
            }
            return true;
        }, false, true, false, $insight);
    }
}
