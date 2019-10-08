<?php

namespace Tests\Endpoints;

use App\Models\Token;
use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test registering new user
     * @return array
     */
    public function test_register()
    {
        // Globals
        $headers = [
            'X-API-TOKEN-ORGANIZATION'    => 'MagicWeb.org EOOD',
            'X-API-CLIENT-APP-IDENTIFIER' => 'izgrev',
        ];
        $baseUrl = env('API_URL') . '/bg';

        // Locals
        $email = date('ymdHis') . env('TEST_USER');
        $data = [
            'first_name'            => 'Unit',
            'last_name'             => 'Test',
            'email'                 => $email,
            'password'              => env('TEST_PASS'),
            'password_confirmation' => env('TEST_PASS'),
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/register", $data);
        // $response->dumpHeaders();
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'info' => trans('notify.info.register', compact('email')),
            ],
            'user'   => [],
        ]);
        // Provide api_token for the other tests
        return [
            'response' => $response->decodeResponseJson(),
            'headers'  => $headers,
            'baseUrl'  => $baseUrl,
        ];
    }

    /**
     * Test resending email verification
     * @depends test_register
     *
     * @param array $args
     */
    public function test_resend_verfification(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = compact('email');

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/resend/verification", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'info' => trans('notify.info.resend-verification', compact('email')),
            ],
            'user'   => [],
        ]);
    }

    /**
     * Test verifying email
     * @depends test_register
     *
     * @param array $args
     */
    public function test_verify_email(array $args)
    {
        // Locals
        $userId = $args['response']['user']['id'];
        $email = $args['response']['user']['email'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = [
            'token' => Token::whereUserId($userId)->first()->hash,
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/verify/email", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'success' => trans('notify.success.verify-email', compact('email')),
            ],
            'user'   => [],
        ]);
        $this->assertNull(Token::whereUserId($userId)->first());
    }

    /**
     * Test signing user into API
     * @depends test_register
     *
     * @param array $args
     *
     * @return array
     */
    public function test_login(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = [
            'email'    => $email,
            'password' => env('TEST_PASS'),
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/login", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'success' => trans('notify.success.login', compact('email')),
            ],
            'user'   => [],
        ]);

        return [
            'headers'  => $headers,
            'baseUrl'  => $baseUrl,
            'response' => $response->decodeResponseJson(),
        ];
    }

    /**
     * Test authentication check
     * @depends test_login
     *
     * @param array $args
     */
    public function test_auth_check(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = [
            'api_token' => $args['response']['user']['api_token'],
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/auth/check", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'info' => trans('notify.info.auth-check', compact('email')),
            ],
            'user'   => [],
        ]);
    }

    /**
     * Test signing user out
     * @depends test_login
     *
     * @param array $args
     */
    public function test_logout(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = [
            'api_token' => $args['response']['user']['api_token'],
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/logout", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'info' => trans('notify.info.logout', compact('email')),
            ],
            'user'   => [
                'api_token' => null,
            ],
        ]);
    }

    /**
     * Test forgetting password
     * @depends test_register
     *
     * @param array $args
     */
    public function test_forgot_password(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = compact('email');

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/forgot/password", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'info' => trans('notify.info.forgot-password', compact('email')),
            ],
            'user'   => [],
        ]);
    }

    /**
     * Test resetting password
     * @depends test_register
     *
     * @param array $args
     */
    public function test_reset_password(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $userId = $args['response']['user']['id'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = [
            'token'                 => Token::whereUserId($userId)->first()->hash,
            'password'              => 'center',
            'password_confirmation' => 'center',
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/reset/password", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'success' => trans('notify.success.reset-password', compact('email')),
            ],
            'user'   => [],
        ]);
    }

    /**
     * Test signing user in with the new password
     * @depends test_register
     *
     * @param array $args
     *
     * @return array
     */
    public function test_login_with_new_password(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = [
            'email'    => $email,
            'password' => 'center',
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/login", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'success' => trans('notify.success.login', compact('email')),
            ],
            'user'   => [],
        ]);

        return [
            'headers'  => $headers,
            'baseUrl'  => $baseUrl,
            'response' => $response->decodeResponseJson(),
        ];
    }

    /**
     * Test request user account deletion
     * @depends test_login_with_new_password
     *
     * @param array $args
     */
    public function test_account_delete_request(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $apiToken = $args['response']['user']['api_token'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = [
            'api_token' => $apiToken,
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/account/delete/request", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'warning' => trans('notify.warning.account-delete-request', compact('email')),
            ],
            'user'   => [],
        ]);
    }

    /**
     * Test user account deletion confirmation
     * @depends test_register
     *
     * @param array $args
     */
    public function test_account_delete_confirm(array $args)
    {
        // Locals
        $email = $args['response']['user']['email'];
        $userId = $args['response']['user']['id'];
        $headers = $args['headers'];
        $baseUrl = $args['baseUrl'];
        $data = [
            'token' => Token::whereUserId($userId)->first()->hash,
        ];

        // Make the request to the tested endpoint
        $response = $this->withHeaders($headers)->postJson("{$baseUrl}/account/delete/confirm", $data);
        // $response->dump();
        $response->assertStatus(200)->assertJson([
            'notify' => [
                'danger' => trans('notify.danger.account-delete-confirm', compact('email')),
            ],
            'user'   => [],
        ]);

        // Outside of API: remove deleted user from DB
        User::onlyTrashed()->find($userId)->forceDelete();
    }

}
