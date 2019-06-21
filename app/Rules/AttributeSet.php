<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AttributeSet implements Rule
{
    protected $failure='';
    protected $failureParams=[];
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $names = [];
        $cpations = [];
        foreach ($value as $customAttribute){
            if(isset($names[$customAttribute['name']])){
                $this->failure = 'duplicate_attribute_name';
                $this->failureParams = ['name' => $customAttribute['name']];
                return false;
            }
            $names[$customAttribute['name']] = true;

            if(isset($captions[$customAttribute['caption']])){
                $this->failure = 'duplicate_attribute_caption';
                $this->failureParams = ['caption' => $customAttribute['caption']];
                return false;
            }
            $captions[$customAttribute['caption']] = true;
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
        return trans('validation.custom_attributes_' . ($this->failure ?? 'unknown'), $this->failureParams);
    }
}
