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
    | Filament Resource
    |--------------------------------------------------------------------------
    |
    | The resource lock filament resource displays all the current locks in place.
    | You are able to replace the resource Lock with your own resource class.
    |
    */
    'resource' => [
        'class' => \Kenepa\ResourceLock\Resources\LockResource::class,
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
    | Lock Notice
    |--------------------------------------------------------------------------
    |
    | The lock notice contains several configuration options for the modal
    | that is display when a resource is locked.
    |
    */

    'lock_notice' => [
        'display_resource_lock_owner' => false,
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
        'navigation_badge' => false,
        'navigation_icon' => 'heroicon-o-lock-closed',
        'navigation_label' => 'Resource Lock Manager',
        'plural_label' => 'Resource Locks',
        'navigation_group' => 'Settings',
        'navigation_sort' => 1,
        'limited_access' => false,
        'should_register_navigation' => true,
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

    /*
   |--------------------------------------------------------------------------
   | Actions
   |--------------------------------------------------------------------------
   |
   | Action classes are simple classes that execute some logic within the package.
   | If you want to add your own custom logic you are able to extend your own
   | class with class your overwriting.
   | Learn more about action classes: https://freek.dev/2442-strategies-for-making-laravel-packages-customizable
   |
   */

    'actions' => [
        'get_resource_lock_owner_action' => \Kenepa\ResourceLock\Actions\GetResourceLockOwnerAction::class,
    ],
];
