<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class Gdpr implements Rule
{
    protected $gdpr;

    public function __construct($gdpr = 1)
    {
        $this->gdpr = $gdpr;
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
        return optional(User::where($attribute, $value)->first())->gdpr === $this->gdpr;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans($this->gdpr ? 'auth.gdpr.off' : 'auth.gdpr.on');
    }
}
