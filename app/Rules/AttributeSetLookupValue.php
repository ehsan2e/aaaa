<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class AttributeSetLookupValue implements Rule
{
    protected $failure = '';
    protected $failureParams = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $values = [];
        $captions = [];
        foreach ($value as $lookupValue) {
            if (isset($values[$lookupValue['value']])) {
                $this->failure = 'duplicate_value';
                $this->failureParams = ['value' => $lookupValue['value']];
                return false;
            }
            $values[$lookupValue['value']] = true;

            if (isset($captions[$lookupValue['caption']])) {
                $this->failure = 'duplicate_caption';
                $this->failureParams = ['caption' => $lookupValue['caption']];
                return false;
            }
            $captions[$lookupValue['caption']] = true;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.lookup_value_' . ($this->failure ?? 'unknown'), $this->failureParams);
    }
}
