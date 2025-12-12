<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class ValidFutureDate implements Rule
{
    private $allowToday;
    private $minDaysInFuture;

    public function __construct($allowToday = false, $minDaysInFuture = 0)
    {
        $this->allowToday = $allowToday;
        $this->minDaysInFuture = $minDaysInFuture;
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
        try {
            $date = Carbon::parse($value);
            $now = Carbon::now();
            
            // Check if date is in the future (or today if allowed)
            if ($this->allowToday) {
                return $date->greaterThanOrEqualTo($now->startOfDay()) && 
                       $date->greaterThanOrEqualTo($now->addDays($this->minDaysInFuture)->startOfDay());
            } else {
                return $date->greaterThan($now->startOfDay()) && 
                       $date->greaterThanOrEqualTo($now->addDays($this->minDaysInFuture)->startOfDay());
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $message = 'The :attribute must be a valid future date';
        
        if ($this->minDaysInFuture > 0) {
            $message .= ' at least ' . $this->minDaysInFuture . ' day(s) from now';
        } elseif (!$this->allowToday) {
            $message .= ' (not today)';
        }
        
        return $message . '.';
    }
}
