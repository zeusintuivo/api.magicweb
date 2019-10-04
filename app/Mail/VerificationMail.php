<?php

namespace App\Mail;

use App\Models\Token;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use function config;
use function encrypt;
use function env;
use function strtoupper;
use function trim;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $appName;
    public $appUrl;
    public $appUrlVerify;

    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $ident = strtoupper($request->header('X-API-CLIENT-APP-IDENTIFIER'));
        $this->appName = env("{$ident}_BRAND");
        $token = Token::create([
            'hash' => encrypt($user->email),
            'user_id' => $user->id
        ]);
        $this->appUrl = trim(env("{$ident}_URL"), '/');
        $this->appUrlVerify =  "{$this->appUrl}/user/verify/email/{$token->hash}";
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        $user = (object) ['email' => $this->user->email, 'name' => $this->user->getFullName()];
        $superUser = (object) ['email' => 'vativa4c@gmail.com', 'name' => config('app.name')];
        return $this->to($user)->bcc($superUser)
            ->subject(trans('auth.email.verification'))
            ->markdown('mail.user.verify_email');
    }
}
