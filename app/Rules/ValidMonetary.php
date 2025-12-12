<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidMonetary implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Must be numeric
        if (!is_numeric($value)) {
            return false;
        }

        $amount = (float) $value;
        
        // Must be non-negative and within reasonable limits (0 to 99999.99)
        return $amount >= 0 && $amount <= 99999.99;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a non-negative amount not exceeding 99,999.99.';
    }
}
