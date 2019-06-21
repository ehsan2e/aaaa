<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    /**
     * @return bool
     */
    public function isClient(): bool
    {
        return $this->role->isClient();
    }

    /**
     * @return bool
     */
    public function isSupplier(): bool
    {
        return $this->role->isSupplier();
    }

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
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
    public function updateProfile(array $data){
        $this->fill($this->prepareProfileData($data));
        return $this->save();
    }
}
