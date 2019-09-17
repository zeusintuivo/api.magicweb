<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */

    'supportsCredentials'    => false,
    'allowedOrigins'         => ['*'],
    'allowedOriginsPatterns' => [],
    'allowedHeaders'         => ['*'],
    'allowedMethods'         => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'exposedHeaders'         => ['CONTENT-DISPOSITION', 'X-CAB7-FNAME-DISPOSITION'],
    'maxAge'                 => 0,

];
