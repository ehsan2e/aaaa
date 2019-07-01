<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;

class Client extends Model
{
    protected $casts = ['active' => 'boolean'];
    protected $fillable = ['first_name', 'last_name'];
    protected $table = 'clients';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    /**
     * @param array $data
     * @param $insight
     * @return Client|null
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public static function createNewClient(array $data, &$insight): ?Client
    {
        return supervisedTransaction(function ($insight) use ($data): ?Client {
            $instance = new static($data);
            $instance->active = isset($data['active']);
            if (!isset($data['create_account'])) {
                return $instance->save() ? $instance : null;
            }
            $user = new User(array_merge($data, ['password' => Hash::make($data['password'])]));
            /** @var Role $role */
            $role = Role::where('type', Role::ROLE_CLIENT)->first();
            if(!$role->users()->save($user)){
                return null;
            }

            $instance->user()->associate($user);
            if(!$instance->save()){
                throw new SupervisedTransactionException('Could not create supplier');
            }

            return $instance;
        }, null, true, false, $insight);
    }
}
