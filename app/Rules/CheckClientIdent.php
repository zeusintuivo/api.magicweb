<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class CheckClientIdent implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($user = User::where($attribute, $value)->first()) {
            return $user->client === request()->header('X-API-CLIENT-APP-IDENTIFIER');
        }
        return false;
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message()
    {
        return trans('validation.auth.client.ident');
    }
}
