<?php

namespace Kenepa\ResourceLock;

use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Kenepa\ResourceLock\Resources\ResourceLockResource;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ResourceLockServiceProvider extends PackageServiceProvider
{
    public static string $name = 'resource-lock';

    protected array $resources = [
        ResourceLockResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasConfigFile()
            ->hasMigration('create_resource_lock_table')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('kenepa/resource-lock');
            });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        Livewire::component('resource-lock-observer', Http\Livewire\ResourceLockObserver::class);

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => Blade::render('@livewire(\'resource-lock-observer\')'),
        );
    }
}
