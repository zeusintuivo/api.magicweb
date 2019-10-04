<?php

namespace App\Http\Requests;

use App\Rules\Active;
use App\Rules\Gdpr;
use App\Rules\Verified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        // Authorize if no concerns
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $name = Route::currentRouteName();
        switch ($name) {
            case 'login':
                return [
                    'email'    => $this->validateEmail('login'),
                    'password' => $this->validatePassword(),
                ];
            case 'register':
                return [
                    'first_name'            => $this->validateFirstName(),
                    'last_name'             => $this->validateLastName(),
                    'email'                 => $this->validateEmail('register'),
                    'password'              => $this->validatePassword(),
                    'password_confirmation' => $this->validatePasswordConfirmation(),
                ];
            case 'verify-email':
                return [
                    'token' => $this->validateToken(),
                ];
            case 'forgot-password':
                return [
                    'email' => $this->validateEmail('forgot-password'),
                ];
            case 'reset-password':
                return [
                    'token'                 => $this->validateToken(),
                    'password'              => $this->validatePassword(),
                    'password_confirmation' => $this->validatePasswordConfirmation(),
                ];
            default:
                return [];
        }
    }

    protected function validateFirstName()
    {
        return 'required_with:last_name|string|min:2';
    }

    protected function validateLastName()
    {
        return 'required_with:first_name|string|min:2';
    }

    protected function validateEmail($form)
    {
        return [
            'required',
            'email',
            $form === 'login' ? new Verified() : null,
            $form === 'login' ? new Active() : null,
            // $form === 'login' ? new Gdpr() : null,
            $form === 'login' ? 'exists:users' : null,
            $form === 'forgot-password' ? 'exists:users' : null,
            $form === 'register' ? 'unique:users' : null,
        ];
    }

    protected function validatePassword()
    {
        return 'required|string|min:6|max:30';
    }

    protected function validatePasswordConfirmation()
    {
        return 'required_with:password|string';
    }

    protected function validateToken()
    {
        return 'required|string|min:60|max:255';
    }
}
