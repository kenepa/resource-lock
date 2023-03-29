<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'models' => [
        'User' => \App\Models\User::class,
//        'ResourceLock' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Unlocker
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'unlocker' => [
        'limited_access' => false,
//        'gate' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | Lock Expire (in minutes)
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'locks_expires' => 10,

    /*
    |--------------------------------------------------------------------------
    | Throw forbidden exception
    |--------------------------------------------------------------------------
    |
    | If a tech savvy user is able to bypass the locked resource modal an 304 forbidden
    | exception will be thrown right before the model is saved. The user will be
    | greeted by a 403 error.
    |
    */

    'throw_forbidden_exception' => true,
];
