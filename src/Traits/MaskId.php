<?php

namespace NovaVoip\Traits;


use Illuminate\Database\Eloquent\Builder;

trait MaskId
{
    public function getRouteKeyName()
    {
        return self::MASK_NAME ?? 'mask';
    }

    public function resolveRouteBinding($value)
    {
        return $this->mask($value)->first() ?? abort(404);
    }


    protected static function getMaskPrefix(): string
    {
        return self::MASK_PREFIX ?? '';
    }

    /**
     * @param Builder $builder
     * @param string $mask
     * @param string|null $table
     */
    public function scopeMask(Builder $builder, string $mask, string $table = null)
    {
        $builder->where((isset($table) ? "{$table}." : '') . 'id', self::decryptMask($mask));
    }

    /**
     * @param string $ticketNumber
     * @return int|null
     */
    public static function decryptMask(string $ticketNumber): ?int
    {
        if (preg_match('/^' . self::getMaskPrefix() . '([0-9a-fA-F]+)$/', $ticketNumber, $matches) !== false) {
            $id = hexdec($matches[1]);
            $id -= self::MASK_OFFSET;
            $id /= self::MASK_MULTIPLIER;
            return (is_int($id) && $id > 0) ? $id : null;
        }
        return null;
    }

    /**
     * @param $id
     * @return string
     */
    public static function generateMask(int $id): string
    {
        return self::getMaskPrefix() . str_pad(strtoupper(dechex($id * self::MASK_MULTIPLIER + self::MASK_OFFSET)), 10, '0', STR_PAD_LEFT);
    }
}