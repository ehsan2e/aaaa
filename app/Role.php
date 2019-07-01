<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    const ROLE_ADMIN = 1;
    const ROLE_CLIENT = 2;
    const ROLE_SUPPLIER = 3;

    protected $parsedAbilities;
    protected $table = 'roles';

    protected function parseAbilities(): array
    {
        if (!isset($this->parsedAbilities)) {
            $this->parsedAbilities = [];
            foreach ($this->abilities as $ability) {
                $this->parsedAbilities[$ability->code] = true;
            }
        }
        return $this->parsedAbilities;
    }

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class, 'role_abilities', 'role_id', 'ability_id', 'id', 'id', 'abilities');
    }

    public function can(string $ability): bool
    {
        $abilities = $this->parseAbilities();
        return isset($abilities[$ability]);
    }

    public function isA(int $type): bool
    {
        return $this->type === $type;
    }

    public function isAdmin(): bool
    {
        return $this->isA(self::ROLE_ADMIN);
    }

    public function isClient(): bool
    {
        return $this->isA(self::ROLE_CLIENT);
    }

    public function isNotA(int $type): bool
    {
        return !$this->isA($type);
    }

    public function isSupplier(): bool
    {
        return $this->isA(self::ROLE_SUPPLIER);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id', 'id', 'id', 'users');
    }
}
