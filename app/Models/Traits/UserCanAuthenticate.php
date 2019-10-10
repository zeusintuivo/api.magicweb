<?php

namespace App\Models\Traits;

use App\Mail\AuthMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use function date;
use function hash;
use function strtoupper;

trait UserCanAuthenticate
{
    public function login()
    {
        $this->api_token = Str::random(60);
        if (request('remember')) {
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

    public function register()
    {
        $this->client = request()->header('X-API-CLIENT-APP-IDENTIFIER');
        $this->first_name = request('first_name');
        $this->last_name = request('last_name');
        $this->email = request('email');
        $this->password = Hash::make(request('password'));
        $this->save();
        return $this->sendAuthMail();
    }

    public function resendVerification()
    {
        return $this->sendAuthMail();
    }

    public function verifyEmail()
    {
        $this->verified = 1;
        $this->active = 1;
        $this->save();
        $this->token->forceDelete();
        return $this;
    }

    public function forgotPassword()
    {
        return $this->sendAuthMail();
    }

    public function resetPassword()
    {
        $this->password = Hash::make(request('password'));
        $this->save();
        $this->token->forceDelete();
        return $this;
    }

    public function accountDeleteRequest()
    {
        // Send confirmation email
        return $this->sendAuthMail();
    }

    public function accountDeleteConfirm()
    {
        $this->logout()->delete();
        $this->token->forceDelete();
        return $this;
    }

    /**
     * Should be called only by admins
     *
     * @return array
     */
    public function accountForceDelete()
    {
        return $this->dumpChildTablesOnAccountDelete();
    }

    protected function sendAuthMail()
    {
        Mail::send(new AuthMail($this));
        $this->last_email_at = date('Y-m-d H:i:s');
        $this->save();
        return $this;
    }

    /**
     * User ID will be needed
     *
     * @return array
     */
    protected function dumpChildTablesOnAccountDelete()
    {
        $client = request()->header('X-API-CLIENT-APP-IDENTIFIER');
        $tables = DB::connection('mysql_information_schema')->select("
            SELECT table_name FROM key_column_usage
                WHERE table_schema = '{$client}' AND referenced_table_name = 'users' AND referenced_column_name = 'id';
        ");
        return $tables;
        //TODO: Select all with this user associated rows and put them into
        // deleted/client_user_id_dump.sql file with insert into statements
    }

}
