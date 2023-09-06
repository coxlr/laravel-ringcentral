<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Credentials
    |--------------------------------------------------------------------------
    |
    | Application credentials. Get your credentials from
    | https://developers.ringcentral.com | 'Credentials - Application Credentials'.
    |
    */
    'client_id'    => function_exists('env') ? env('RINGCENTRAL_CLIENT_ID', '') : '',
    'client_secret' => function_exists('env') ? env('RINGCENTRAL_CLIENT_SECRET', '') : '',
    'server_url' => function_exists('env') ? env('RINGCENTRAL_SERVER_URL', '') : '',

    /*
    |--------------------------------------------------------------------------
    | Operator Extension Credentials
    |--------------------------------------------------------------------------
    |
    | If you're using client credentials, change these settings. Get your
    | credentials from https://developers.ringcentral.com | 'Credentials - User Account Credentials'.
    |
    */
    'username' => function_exists('env') ? env('RINGCENTRAL_USERNAME', '') : '',
    'operator_token' => function_exists('env') ? env('RINGCENTRAL_OPERATOR_TOKEN', '') : '',

    /*
    |--------------------------------------------------------------------------
    | Admin Extension Credentials
    |--------------------------------------------------------------------------
    |
    | If you operator is not your admin you will need to supply the admin jwt
    | to be able to access functions other than sent sms
    |
    */
    'admin_token' => function_exists('env') ? env('RINGCENTRAL_ADMIN_TOKEN', '') : '',
];
