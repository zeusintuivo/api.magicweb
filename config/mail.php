<?php

return [
    'driver'     => env('MAIL_DRIVER'),
    'sendmail'   => '/usr/sbin/sendmail -bs',
    'encryption' => env('MAIL_ENCRYPTION'),
    'host'       => env('MAIL_HOST'),
    'port'       => env('MAIL_PORT'),
    'username'   => env('MAIL_USERNAME'),
    'password'   => env('MAIL_PASSWORD'),
    'markdown'   => [
        'theme' => 'custom',
        'paths' => [resource_path('views/vendor/mail')],
    ],
];
