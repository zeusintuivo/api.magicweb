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

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $appName;
    public $appUrl;
    public $appUrlReset;

    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $ident = strtoupper($request->header('X-API-CLIENT-APP-IDENTIFIER'));
        $this->appName = env("{$ident}_BRAND");
        $token = Token::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'hash' => encrypt($user->email),
        ]);
        $this->appUrl = trim(env("{$ident}_URL"), '/');
        $this->appUrlReset =  "{$this->appUrl}/user/reset/password/{$token->hash}";
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        $user = (object) ['email' => $this->user->email, 'name' => $this->user->getFullName()];
        $superUser = (object) ['email' => 'vativa4c@gmail.com', 'name' => config('app.name')];
        // dd($user, $superUser);
        return $this->to($user)->bcc($superUser)
            ->subject(trans('auth.email.reset.password.label'))
            ->markdown('mail.user.reset_password');
    }
}
