<?php

namespace App\Rules\Dates;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MinRentTime implements Rule
{
    private $time_to;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($time_to)
    {
        $this->time_to = Carbon::parse($time_to);
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
        $time_from = Carbon::parse($value);
        $length = $time_from->diffInMinutes($this->time_to)/60/24;
        return $length>=1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Минимально возможное время забора: '.$this->time_to->addHours(24)->format('d.m.Y H:i');
    }
}
