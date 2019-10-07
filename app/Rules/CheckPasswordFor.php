<?php

namespace App\Rules;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use function dd;
use function trans;

class CheckPasswordFor implements Rule
{
    protected $user;

    /**
     * Create a new rule instance.
     *
     * @param $email string
     * @return void
     */
    public function __construct(string $email)
    {
        $this->user = User::whereEmail($email)->first();
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
        return $this->user ? Hash::check($value, $this->user->password) : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('passwords.failed');
    }
}
