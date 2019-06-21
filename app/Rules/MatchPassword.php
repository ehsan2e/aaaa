<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class MatchPassword implements Rule
{
    /**
     * @var string
     */
    protected $currentPassword;

    /**
     * Create a new rule instance.
     *
     * @param string $curentPassword
     */
    public function __construct(string $curentPassword)
    {
        $this->currentPassword = $curentPassword;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, $this->currentPassword);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.match_password');
    }
}
