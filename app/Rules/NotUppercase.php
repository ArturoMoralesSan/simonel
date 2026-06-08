<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotUppercase implements Rule
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
        return $value !== mb_strtoupper($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No utilices únicamente mayúsculas.';
    }
}
