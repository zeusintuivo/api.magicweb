<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\DatabaseRule;
use function compact;

class EmailExists implements Rule
{
    use DatabaseRule;

    protected $email;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->email = $value;
        return (bool) User::whereEmail($this->email)->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $email = $this->email;
        return trans('validation.auth.email.exists', compact('email'));
    }
}
