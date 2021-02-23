<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckTimeWindow implements Rule
{
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
        $last_month_end =  date('Y-m-d', strtotime('last day of previous month'));
        
        return $value <= $last_month_end;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please select a day in previous month';
    }
}
