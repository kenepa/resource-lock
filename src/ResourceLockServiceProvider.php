<?php

namespace Kenepa\ResourceLock;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;

class ResourceLockServiceProvider extends PluginServiceProvider
{
    public static string $name = 'resource-lock';

    protected array $resources = [
        // CustomResource::class,
    ];

    protected array $pages = [
        // CustomPage::class,
    ];

    protected array $styles = [
        'plugin-resourcelock' => __DIR__ . '/../resources/dist/resourcelock.css',
    ];

    protected array $scripts = [
        'plugin-resourcelock' => __DIR__ . '/../resources/dist/resourcelock.js',
    ];

    // protected array $beforeCoreScripts = [
    //     'plugin-resourcelock' => __DIR__ . '/../resources/dist/resourcelock.js',
    // ];

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasConfigFile()
            ->hasMigration('create_resource_lock_table')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('kenepa/resource-lock');
            });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        Livewire::component('resource-lock-observer', Http\Livewire\ResourceLockObserver::class);

        Filament::registerRenderHook(
            'body.end',
            fn(): string => Blade::render('@livewire(\'resource-lock-observer\')'),
        );
    }
}
