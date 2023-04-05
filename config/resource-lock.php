<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | The models configuration specifies the classes that represent your application's
    | data objects. This configuration is used by the framework to interact with
    | the application's data models. You can even implement your own ResourceLock model.
    |
    */

    'models' => [
        'User' => \App\Models\User::class,
        // 'ResourceLock' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Unlocker
    |--------------------------------------------------------------------------
    |
    | The unlocker configuration specifies whether limited access is enabled for
    | the resource lock feature. If limited access is enabled, only specific
    | users or roles will be able to unlock locked resources.
    |
    */

    'unlocker' => [
        'limited_access' => false,
        // 'gate' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | Lock timeout (in minutes)
    |--------------------------------------------------------------------------
    |
    | The lock_timeout configuration specifies the time interval, in minutes,
    | after which a lock on a resource will expire if it has not been manually
    | unlocked or released by the user.
    |
    */

    'lock_timeout' => 10,

    /*
    |--------------------------------------------------------------------------
    | Check Locks before saving
    |--------------------------------------------------------------------------
    |
    | The check_locks_before_saving configuration specifies whether a lock of a resource will be checked
    | before saving a resource if a tech-savvy user is able to bypass the locked
    | resource modal and attempt to save the resource. In some cases you may want to turns this off.
    | It's recommended to keep this on.
    |
    */

    'check_locks_before_saving' => true,
];

