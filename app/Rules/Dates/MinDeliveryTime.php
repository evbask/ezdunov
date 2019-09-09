<?php

namespace App\Rules\Dates;
use Carbon\Carbon;

use Illuminate\Contracts\Validation\Rule;

class MinDeliveryTime implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

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
        $end = Carbon::parse($value);
        $now = Carbon::now();
        $length = $end->diffInMinutes($now);
        return $length >= 58;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Минимально возможное время доставки: '.Carbon::now()->addHours(1)->format('H:i');
    }
}
