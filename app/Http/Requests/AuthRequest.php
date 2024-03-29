<?php

namespace App\Http\Requests;

use App\Rules\Active;
use App\Rules\CheckClientIdent;
use App\Rules\CheckPasswordFor;
use App\Rules\EmailExists;
use App\Rules\EmailUnique;
use App\Rules\TokenDecrypts;
use App\Rules\TokenExists;
use App\Rules\TokenExpires;
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
        switch (Route::currentRouteName()) {
            case 'login':
                return [
                    'email'    => $this->validateEmail('login'),
                    'password' => $this->validatePassword('login'),
                ];
            case 'register':
                return [
                    'first_name' => $this->validateFirstName(),
                    'last_name'  => $this->validateLastName(),
                    'email'      => $this->validateEmail('register'),
                    'password'   => $this->validatePassword('register'),
                ];
            case 'verify/email':
            case 'account/delete/confirm':
                return [
                    'token' => $this->validateToken(),
                ];
            case 'forgot/password':
                return [
                    'email' => $this->validateEmail(),
                ];
            case 'resend/verification':
                return [
                    'email' => $this->validateEmail('resend-verification'),
                ];
            case 'reset/password':
                return [
                    'token'    => $this->validateToken(),
                    'password' => $this->validatePassword(),
                ];
            case 'account/delete/request':
                return [
                    'api_token' => new CheckClientIdent(),
                ];
            default:
                return [];
        }
    }

    protected function validateFirstName()
    {
        return 'required|string|min:2';
    }

    protected function validateLastName()
    {
        return 'required|string|min:2';
    }

    protected function validateEmail($form = 'any')
    {
        return [
            'bail',
            'required',
            'email',
            $form === 'register' ? new EmailUnique('users') : new EmailExists('users'),
            $form === 'login' ? new Verified() : null,
            $form === 'login' ? new Active() : null,
            // $form === 'login' ? new Gdpr() : null,
            $form === 'resend-verification' ? new Verified(0) : null,
        ];
    }

    protected function validatePassword($form = 'any')
    {
        $email = $this->input('email');
        return [
            'required',
            'string',
            'min:6',
            'max:30',
            $form === 'login' ? new CheckPasswordFor($email) : 'confirmed',
        ];
    }

    protected function validateToken()
    {
        return [
            'bail',
            'required',
            'string',
            'size:60',
            new TokenExists(),
            new TokenExpires(),
        ];
    }
}
