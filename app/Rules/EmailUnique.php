<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\DatabaseRule;
use function compact;

class EmailUnique implements Rule
{
    use DatabaseRule;

    protected $email;
    protected $user;

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
        $this->user = User::withTrashed()->whereEmail($this->email)->first();
        return !$this->user;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $email = $this->email;
        return trans($this->user && $this->user->trashed() ? 'validation.auth.email.trashed' : 'validation.auth.email.unique', compact('email'));
    }
}
