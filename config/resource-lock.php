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
    | Resource Unlocker Button
    |--------------------------------------------------------------------------
    |
    | The unlocker configuration specifies whether limited access is enabled for
    | the resource unlock button. If limited access is enabled, only specific
    | users or roles will be able to unlock locked resources directly from
    | the modal.
    |
    */

    'unlocker' => [
        'limited_access' => false,
        // 'gate' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Lock Manager
    |--------------------------------------------------------------------------
    |
    | The resource lock manager provides a simple way to view all resource locks
    | of your application. It provides several ways to quickly unlock all or
    | specific resources within your app.
    |
    */

    'manager' => [
        'navigation_label' => 'Resource Lock Manager',
        'navigation_group' => 'Settings',
        'navigation_sort' => 1,
        'limited_access' => false,
//        'gate' => ''
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

