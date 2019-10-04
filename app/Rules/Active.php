<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class Active implements Rule
{
    protected $active;

    public function __construct($active = 1)
    {
        $this->active = $active;
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
        return optional(User::where($attribute, $value)->first())->active === $this->active;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans($this->active ? 'auth.active.off' : 'auth.active.on');
    }
}
