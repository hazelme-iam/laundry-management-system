<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidWeight implements Rule
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

        $weight = (float) $value;
        
        // Must be positive and within reasonable limits (0.1kg to 100kg)
        return $weight >= 0.1 && $weight <= 100;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a positive number between 0.1 and 100 kilograms.';
    }
}
