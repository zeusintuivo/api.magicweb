<?php

namespace App\Mail;

use App\Models\Token;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Route;
use stdClass;
use function config;
use function dd;
use function encrypt;
use function env;
use function strtoupper;
use function trim;

class AuthMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $client;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->client = new stdClass();
        $this->client->ident = strtoupper(request()->header('X-API-CLIENT-APP-IDENTIFIER'));
        $this->client->brand = env("{$this->client->ident}_BRAND");
        $token = Token::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'hash' => encrypt($user->email),
        ]);
        $this->client->baseUrl = trim(env("{$this->client->ident}_URL"), '/');
        $this->client->routeName = Route::currentRouteName();
        $this->client->tokenUrl = "{$this->client->baseUrl}/{$this->client->routeName}/{$token->hash}";
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        $user = (object) ['email' => $this->user->email, 'name' => $this->user->getFullName()];
        $superUser = (object) ['email' => 'vativa4c@gmail.com', 'name' => $this->client->brand];
        return $this->to($user)->bcc($superUser)
            ->subject(trans("auth.mail.{$this->client->routeName}.label"))
            ->markdown('mail.user.auth');
    }
}
