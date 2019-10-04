<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class Verified implements Rule
{
    protected $verified;

    public function __construct($verified = 1)
    {
        $this->verified = $verified;
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
        return optional(User::where($attribute, $value)->first())->verified === $this->verified;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans($this->verified ? 'auth.verified.off' : 'auth.verified.on');
    }
}
