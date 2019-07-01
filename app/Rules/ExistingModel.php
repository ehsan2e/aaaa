<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistingModel implements Rule
{
    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $builder;
    /**
     * @var string
     */
    protected $field = 'id';
    /**
     * @var string
     */
    protected $message = 'validation.existing_model';
    /**
     * @var string
     */
    protected $table;

    /**
     * Create a new rule instance.
     *
     * @param string $table
     * @param string $field
     */
    public function __construct(string $table, string $field = 'id')
    {
        $this->table = $table;
        $this->field = $field;
        $this->builder = DB::table($table);
    }

    public function __call($name, $arguments)
    {
        return $this->builder->{$name}(...$arguments);
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
        if(!isset($value)){
            return false;
        }
        return (bool) $this->builder->where($this->field, $value)->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ExistingModel
     */
    public function setMessage(string $message): ExistingModel
    {
        $this->message = $message;
        return $this;
    }
}
