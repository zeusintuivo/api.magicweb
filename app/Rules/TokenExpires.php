<?php

namespace App\Rules;

use App\Exceptions\TokenExpiredException;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use function config;

class TokenExpires implements Rule
{
    protected $range;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->range = config('auth.passwords.users.expire');
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
        $timestamp = Token::find($value)->updated_at;
        $diff = Carbon::now()->diffInMinutes($timestamp);
        return $this->range > $diff;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $minutes = $this->range;
        return trans('validation.token.expires', compact('minutes'));
    }
}
