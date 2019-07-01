<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

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

    /**
     * @param array $data
     * @return array
     */
    protected function prepareProfileData(array $data): array
    {
        return $data;
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
     * @return HasOne
     */
    public function client(): HasOne
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    /**
     * @param string $ability
     * @return bool
     */
    public function isAble(string $ability): bool
    {
        /** @var Role $role */
        foreach ($this->roles as $role) {
            if ($role->can($ability)) {
                return true;
            }
        }
        return false;
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

    /**
     * @param array $data
     * @return bool
     */
    public function updateProfile(array $data)
    {
        $this->fill($this->prepareProfileData($data));
        return $this->save();
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
}
