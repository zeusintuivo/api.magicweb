<?php

namespace App\Models\Traits;

use App\Mail\ResetPasswordMail;
use App\Mail\VerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use function dd;
use function hash;
use function strtoupper;

trait UserCanAuthenticate
{
    public function login(Request $request)
    {
        $this->api_token = Str::random(60);
        if ($request->input('remember')) {
            $this->remember = 1;
        }
        $this->save();
        return $this;
    }

    public function logout()
    {
        $this->api_token = null;
        $this->remember = null;
        $this->save();
        return $this;
    }

    public function register(Request $request)
    {
        $this->client = $request->header('X-API-CLIENT-APP-IDENTIFIER');
        $this->first_name = $request['first_name'];
        $this->last_name = $request['last_name'];
        $this->email = $request['email'];
        $this->password = hash('sha256', $request['password']);
        $this->save();
        Mail::send(new VerificationMail($this, $request));
        return $this;
    }

    public function verifyEmail()
    {
        $this->verified = 1;
        $this->active = 1;
        $this->save();
        $this->token->forceDelete();
        return $this;
    }

    public function forgotPassword(Request $request)
    {
        Mail::send(new ResetPasswordMail($this, $request));
        return $this;
    }

    public function resetPassword(string $password)
    {
        $this->password = hash('sha256', $password);
        $this->save();
        $this->token->forceDelete();
        return $this;
    }
}
