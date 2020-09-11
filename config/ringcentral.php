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
    'operator_extension' => function_exists('env') ? env('RINGCENTRAL_OPERATOR_EXTENSION', '') : '',
    'operator_password' => function_exists('env') ? env('RINGCENTRAL_OPERATOR_PASSWORD', '') : '',

    /*
    |--------------------------------------------------------------------------
    | Admin Extension Credentials
    |--------------------------------------------------------------------------
    |
    | If you operator extension is not your admin extension you will need to
    | supply the admin extnesion details to be able to access functions other
    | than sent sms
    |
    */
    'admin_extension' => function_exists('env') ? env('RINGCENTRAL_ADMIN_EXTENSION', '') : '',
    'admin_password' => function_exists('env') ? env('RINGCENTRAL_ADMIN_PASSWORD', '') : '',
];
