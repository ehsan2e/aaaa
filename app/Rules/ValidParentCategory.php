<?php

namespace App\Rules;

use App\PostCategory;
use Illuminate\Contracts\Validation\Rule;

class ValidParentCategory implements Rule
{
    protected $forbiddenId;
    protected $language;
    protected $parentId;

    /**
     * Create a new rule instance.
     *
     * @param string $language
     */
    public function __construct(string $language)
    {
        $this->language = $language;
    }

    public function forbidden(int $id): ValidParentCategory
    {
        $this->forbiddenId = $id;
        return $this;
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
            return true;
        }
        if(isset($this->forbiddenId) && ($this->forbiddenId == $value)){
            return false;
        }
        $postCategory = PostCategory::where('language', $this->language)->find($value);

        return isset($postCategory) && ((!isset($this->forbiddenId)) || (strpos($postCategory->address, "-{$this->forbiddenId}-") === false));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.valid_parent_category');
    }
}
