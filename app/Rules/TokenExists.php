<?php

namespace App\Rules;

use App\Models\EmailAuthentication;
use Illuminate\Contracts\Validation\Rule;

class TokenExists implements Rule
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
        return EmailAuthentication::whereToken($value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.token.exists');
    }
}
