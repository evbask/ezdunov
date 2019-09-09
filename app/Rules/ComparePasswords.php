<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Components\Toolkit;
class ComparePasswords implements Rule
{
    private $user_pass;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user_pass)
    {
        $this->user_pass = $user_pass;
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
        if(Toolkit::comparePasswords($value,$this->user_pass)){
            return true;
        }else{
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
        return 'Cтарый пароль введен неверно';
    }
}
