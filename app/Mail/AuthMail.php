<?php

namespace App\Mail;

use App\Models\EmailAuthentication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use stdClass;
use function config;
use function strtoupper;

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
        $this->client->brand = config("client.{$this->client->ident}.brand");
        $token = EmailAuthentication::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'token' => Str::random(60)
        ]);
        $this->client->baseUrl = trim(env("CLIENT_URL_{$this->client->ident}"), '/');
        $this->client->routeName = Route::currentRouteName();
        $this->client->tokenUrl = "{$this->client->baseUrl}/{$this->client->routeName}/{$token->token}";
        $this->client->buttonColor = $this->client->routeName === 'account/delete/request' ? 'error' : 'primary';
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        return $this->from((object)config("client.{$this->client->ident}.from"))
            ->to((object)['email' => $this->user->getEmail(), 'name' => $this->user->getFullName()])
            ->bcc((object)config("client.{$this->client->ident}.reply"))
            ->replyTo((object)config("client.{$this->client->ident}.reply"))
            ->subject(trans("auth.mail.{$this->client->routeName}.label"))
            ->markdown('mail.user.auth');
    }
}
