<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use NovaVoip\Exceptions\SupervisedTransactionException;
use NovaVoip\Interfaces\iTransactionCollectionGenerator;
use NovaVoip\InvoiceProcessor\FastForward;
use function NovaVoip\supervisedTransaction;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @var boolean|null
     */
    protected $userIsAnAdmin;

    /**
     * @var boolean|null
     */
    protected $userIsAClient;

    /**
     * @var boolean|null
     */
    protected $userIsASupplier;

    protected static $cache = [];

    /**
     * @param array $data
     * @return array
     */
    protected function prepareProfileData(array $data): array
    {
        return $data;
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }

    /**
     * @param string $password
     * @return bool
     */
    public function changePassword(string $password): bool
    {
        $this->password = Hash::make($password);
        return $this->save();
    }

    /**
     * @param float $amount
     * @return Invoice|null
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function chargeWallet(float $amount): ?Invoice
    {
        $amount = round((float) $amount, 2);
        return supervisedTransaction(function() use ($amount){
            $invoice = new Invoice();
            $invoice->sub_total = $amount;
            $invoice->grand_total = $amount;
            $invoice->tag = 'charge_wallet';
            $invoice->can_be_paid_by_credit = false;
            $invoice->processor = FastForward::class;
            $invoice->client()->associate($this->client);
            if(!$invoice->save()){
                return null;
            }

            $invoiceItem = new InvoiceItem();
            $invoiceItem->amount = 1;
            $invoiceItem->price = $amount;
            $invoiceItem->sub_total = $amount;
            $invoiceItem->grand_total = $amount;
            $invoiceItem->description = 'Charging wallet';
            $invoiceItem->tag = 'charge_wallet';
            $invoiceItem->invoice()->associate($invoice);
            if(!$invoiceItem->save()){
                throw new SupervisedTransactionException('Could not create invoice item');
            }
            return $invoice;
        }, null, true, false);
    }

    /**
     * @return HasOne
     */
    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'user_id', 'id');
    }

    /**
     * @param iTransactionCollectionGenerator $transactionCollectionGenerator
     * @param null $insight
     * @return bool
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function createTransaction(iTransactionCollectionGenerator $transactionCollectionGenerator, &$insight=null): bool
    {
        return Transaction::createTransaction($this, $transactionCollectionGenerator, null, $insight);
    }

    /**
     * @param string $modelClass
     * @return array
     */
    public function getModelAccessibleFields(string $modelClass): array
    {
        if($this->can('model_access_all')){
            return ['*'];
        }

        return [];
    }

    /**
     * @param string $modelClass
     * @return array
     */
    public function getModelAccessibleRelations(string $modelClass): array
    {
        if($this->can('model_access_all')){
            return ['*'];
        }

        return [];
    }

    /**
     * @param string $ability
     * @return bool
     */
    public function isAble(string $ability): bool
    {
        if(!isset(self::$cache[$ability])){
            self::$cache[$ability] = false;
            /** @var Role $role */
            foreach ($this->roles as $role) {
                if ($role->can($ability)) {
                    self::$cache[$ability] = true;
                    break;
                }
            }
        }
        return self::$cache[$ability];
    }

    /**
     * @param int $type
     * @return bool
     */
    public function isA(int $type): bool
    {
        /** @var Role $role */
        foreach ($this->roles as $role) {
            if ($role->isA($type)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        if (!isset($this->userIsAnAdmin)) {
            $this->userIsAnAdmin = false;
            foreach ($this->roles as $role) {
                if ($role->isAdmin()) {
                    $this->userIsAnAdmin = true;
                    break;
                }
            }
        }
        return $this->userIsAnAdmin;
    }

    /**
     * @return bool
     */
    public function isClient(): bool
    {
        if (!isset($this->userIsAClient)) {
            $this->userIsAClient = false;
            foreach ($this->roles as $role) {
                if ($role->isClient()) {
                    $this->userIsAClient = true;
                    break;
                }
            }
        }
        return $this->userIsAClient;
    }

    /**
     * @param int $type
     * @return bool
     */
    public function isNotA(int $type): bool
    {
        return !$this->isA($type);
    }

    /**
     * @return bool
     */
    public function isSupplier(): bool
    {
        if (!isset($this->userIsASupplier)) {
            $this->userIsASupplier = false;
            foreach ($this->roles as $role) {
                if ($role->isSupplier()) {
                    $this->userIsASupplier = true;
                    break;
                }
            }
        }
        return $this->userIsASupplier;
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id', 'id', 'id', 'roles');
    }

    /**
     * @return HasOne
     */
    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'user_id', 'id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }

    /**
     * @param array $data
     * @return bool
     */
    public function updateProfile(array $data)
    {
        $this->fill($this->prepareProfileData($data));
        return $this->save();
    }
}
