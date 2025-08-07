# Resource Lock

<a href="https://github.com/kenepa/resource-lock" class="filament-hidden">
<img style="width: 100%; max-width: 100%;" alt="filament-resource-lock-art" src="https://raw.githubusercontent.com/kenepa/Kenepa/main/art/ResourceLock/filament-resource-log-banner.png" >
</a>


[![Latest Version on Packagist](https://img.shields.io/packagist/v/kenepa/resource-lock.svg?style=flat-square)](https://packagist.org/packages/kenepa/resource-lock)
[![Total Downloads](https://img.shields.io/packagist/dt/kenepa/resource-lock.svg?style=flat-square)](https://packagist.org/packages/kenepa/resource-lock)

Filament Resource Lock is a Filament plugin that adds resource locking functionality to your site. When a
user begins editing a resource, Filament Resource Lock automatically locks the resource to prevent other users from
editing it at the same time. The resource will be automatically unlocked after a set period of time, or when the user
saves or discards their changes.

<img style="width: 100%; max-width: 100%;" alt="filament-resource-lock-art" src="https://raw.githubusercontent.com/kenepa/Kenepa/main/art/ResourceLock/filament-resource-lock-demo.gif" >

## Installation

| Plugin Version | Filament Version | PHP Version |
|----------------|------------------|-------------|
| 1.x            | 2.x              | \> 8.0      |
| 2.x            | 3.x              | \> 8.1      |
| 3.x            | 3.x              | \> 8.1      |

You can install the package via composer:

```bash
composer require kenepa/resource-lock
```

Then run the installation command to publish and run migration(s)

```bash
php artisan resource-lock:install
```

Register plugin with a panel
```php
use Kenepa\ResourceLock\ResourceLockPlugin;
use Filament\Panel;
 
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(ResourceLockPlugin::make());
}
```

# Upgrade guides

## Upgrade to 2.x

> Notice - Upgrading to Version 2.1.x :  
> In case you have published the config, make sure to update the following in your config:
> ```php
>    'resource' => [
>        'class' => \Kenepa\ResourceLock\Resources\LockResource::class,
>    ],
> ```

## Upgrade from 2.x to 3.x

### Breaking Changes

- **Plugin-based Customization**: Icons, labels, model classes, and access gates now configured via the plugin.
- **Timeout unit changed**: Now uses seconds instead of minutes

## Usage

The Filament Resource Lock package enables you to lock a resource and prevent other users from editing it at the same
time. Currently, this package only locks
the [EditRecord](https://filamentphp.com/docs/2.x/admin/resources/editing-records) page and the edit modal when editing
a [simple modal resource.](https://filamentphp.com/docs/2.x/admin/resources/getting-started#simple-modal-resources)
Follow the steps below to add locks to your resources.

### Add Locks to your model

The first step is to add the HasLocks trait to the model of your resource. The HasLocks trait enables the locking
functionality on your model.

```php
// Post.php

use Kenepa\ResourceLock\Models\Concerns\HasLocks;

class Post extends Model
{
    use HasFactory;
    use HasLocks;

    protected $table = 'posts';

    protected $guarded = [];
}
```

### Add Locks to your EditRecord Page

The second step is to add the UsesResourceLock trait to your EditRecord page. The UsesResourceLock trait enables the
locking function on your edit page.

```php
// EditPost.php

use Kenepa\ResourceLock\Resources\Pages\Concerns\UsesResourceLock;

class EditPost extends EditRecord
{
    use UsesResourceLock;

    protected static string $resource = PostResource::class;
}
```

#### Simple modal Resource

If your resource is
a [simple modal](https://filamentphp.com/docs/2.x/admin/resources/getting-started#simple-modal-resources) resource,
you'll need to use the UsesSimpleResourceLock trait instead.

```php
// ManagePosts.php

use Kenepa\ResourceLock\Resources\Pages\Concerns\UsesSimpleResourceLock;

class ManagePosts extends ManageRecords
{
    use UsesSimpleResourceLock;

    protected static string $resource = PostResource::class;

}
```

And that's it! Your resource is now able to be locked. Refer to the documentation below for more information on how to
configure the locking functionality.

## Use polling to detect presence (SPA mode)

To make the resource locking feature possible for SPA we polling based detection to check if a user is still editing the resource.
This is disabled by default but you can enable it in the config:

```php
use Kenepa\ResourceLock\ResourceLockPlugin;
use Filament\Panel;
 
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(ResourceLockPlugin::make()
        ->usesPollingToDetectPresence()
        ->presencePollingInterval(10));
}
```

### Polling Configuration

When using polling to detect presence, you can configure additional options (by default Livewire will reduce the number of polling requests by 95% until the user revisits the tab.

):

- **`pollingKeepAlive()`**: Keeps the polling connection alive even when the user has the tab in the background.
- **`pollingVisible()`**: Only polls when the browser tab is visible to the user. This helps reduce server load by pausing polling when users switch to other tabs.

## Resource Lock manager

<img style="width: 100%; max-width: 100%;" alt="filament-resource-lock-art" src="https://raw.githubusercontent.com/kenepa/Kenepa/main/art/ResourceLock/filament-resource-lock-manager.png" >

The package also provides a simple way to manage and view all your active and expired locks within your app. And it also
provides a way to quickly unlock all resources or specific locks.

## Configuration

### Access

<img style="width: 100%; max-width: 100%;" alt="filament-resource-lock-art" src="https://raw.githubusercontent.com/kenepa/Kenepa/main/art/ResourceLock/filament-locked.png" >

You can restrict the access to the **Unlock** button or to the resource manager by adjusting the access variable.
Enabling the "limited" key and
setting it to true allows you to specify either a Laravel Gate class or a permission name from
the [Spatie Permissions package](https://github.com/spatie/laravel-permission).

```php
use Kenepa\ResourceLock\ResourceLockPlugin;
use Filament\Panel;
 
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->limitedAccessToResourceLockManager()
        ->gate('unlock')
}
```

Example

```php

// Example using gates
// More info about gates: https://laravel.com/docs/authorization#writing-gates
Gate::define('unlock', function (User $user, Post $post) {
  return $user->email === 'admin@mail.com';
});

// Example using spatie permission package
Permission::create(['name' => 'unlock']);
```

### Using custom models

Sometimes, you may have a customized implementation for the User model in your application, or you may want to use a
custom class for the ResourceLock functionality. In such cases, you can update the configuration file to specify the new
class you want to use. This will ensure that the ResourceLock functionality works as expected with the new
implementation.

```php
use Kenepa\ResourceLock\ResourceLockPlugin;
use Filament\Panel;
 
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->userModel(\App\Models\CustomUser::class)
        ->resourceLockModel(\App\Models\CustomResourceLock::class);
}
```

### Displaying the user who has locked the resource

This package uses actions which allows you to implement your own custom logic. An action class is nothing more than a
simple class with a method that executes some
logic. [Learn more about actions](https://freek.dev/2442-strategies-for-making-laravel-packages-customizable)

To create a custom action, first create a file within your project and name
it ```CustomGetResourceLockOwnerAction.php```, for
example. In this file, create a new class that extends the ```GetResourceLockOwnerAction``` class and override the
execute
method to return the desired identifier. For example:

```php
// CustomGetResourceLockOwnerAction.php

namespace App\Actions;

use Kenepa\ResourceLock\Actions\GetResourceLockOwnerAction;

class CustomResourceLockOwnerAction extends GetResourceLockOwnerAction
{
    public function execute($userModel): string|null
    {
        return $userModel->email;
    }
}
```

Next, register your custom action within the your plugin configuration:

```php
use Kenepa\ResourceLock\ResourceLockPlugin;
use Filament\Panel;
 
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(
            ResourceLockPlugin::make()
                ->resourceLockOwnerAction(\Kenepa\ResourceLock\Actions\CustomGetResourceLockOwnerAction::class)
        );
}
```

### Overriding default functionality

If you need some custom functionality beyond what the traits provide, you can override the functions that they use. For
example, if you want to change the URL that the "Return" button redirects to, you can override the
resourceLockReturnUrl() function. By default, this button takes you to the index page of the resource, but you can
change it to whatever URL you want by adding your custom implementation in the resourceLockReturnUrl() function.

For instance, if you want the "Return" button to redirect to https://laracasts.com, you can override the function as
follows:

```php
     public function resourceLockReturnUrl(): string 
    {
        return 'https://laracasts.com';
    }
```

Now the return url will redirect to laracasts.com

This will change the behavior of the "Return" button to redirect to the provided URL.

## Publishing migrations, configuration and view

```bash
php artisan vendor:publish --tag="resource-lock-migrations"
php artisan migrate
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="resource-lock-migrations"
php artisan migrate
```

Optionally, you can publish the views using

> Note: Publishing Blade views can introduce breaking changes into your app. If you're interested in how to stay
> safe, [see this article by Dan Harrin](https://filamentphp.com/blog/publishing-views-in-laravel).

```bash
php artisan vendor:publish --tag="resource-lock-views"
```

## Coming soon

- Locked status indicator for table rows
- Polling
- Optimistic Locking

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
