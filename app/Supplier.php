<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;

class Supplier extends Model
{
    protected $casts = ['active' => 'boolean'];
    protected $fillable = ['name'];
    protected $table = 'suppliers';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @param array $data
     * @param $insight
     * @return bool
     * @throws SupervisedTransactionException
     * @throws \Exception
     */
    public function updateInfo(array $data, &$insight): bool
    {
        return supervisedTransaction(function ($insight) use ($data): bool {
            /** @var Supplier $instance */
            $instance = Supplier::lockForUpdate()->find($this->id);
            $instance->fill($data);
            $instance->active = isset($data['active']);
            if(isset($instance->user_id) || (!isset($data['create_account']))){
                return $instance->save();
            }

            $user = new User(array_merge($data, ['password' => Hash::make($data['password'])]));
            /** @var Role $role */
            $role = Role::where('type', Role::ROLE_SUPPLIER)->first();
            if(!$role->users()->save($user)){
                return false;
            }

            $instance->user()->associate($user);
            if(!$instance->save()){
                throw new SupervisedTransactionException('Could not update supplier');
            }

            return true;
        }, false, false, false, $insight);

    }

    /**
     * @param array $data
     * @param $insight
     * @return Supplier|null
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public static function createNewSupplier(array $data, &$insight): ?Supplier
    {
        return supervisedTransaction(function ($insight) use ($data): ?Supplier {
            $instance = new static($data);
            $instance->active = isset($data['active']);
            if (!isset($data['create_account'])) {
                return $instance->save() ? $instance : null;
            }
            $user = new User(array_merge($data, ['password' => Hash::make($data['password'])]));
            /** @var Role $role */
            $role = Role::where('type', Role::ROLE_SUPPLIER)->first();
            if(!$role->users()->save($user)){
                return null;
            }

            $instance->user()->associate($user);
            if(!$instance->save()){
                throw new SupervisedTransactionException('Could not create supplier');
            }

            return $instance;
        }, null, false, false, $insight);
    }
}
