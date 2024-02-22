<?php

namespace Kenepa\ResourceLock;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Kenepa\ResourceLock\Resources\ResourceLockResource;
use Livewire\Livewire;

class ResourceLockPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'resource-lock';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                config('resource-lock.resource.class', ResourceLockResource::class),
            ]);
    }

    public function boot(Panel $panel): void
    {
        Livewire::component('resource-lock-observer', Http\Livewire\ResourceLockObserver::class);

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => Blade::render('@livewire(\'resource-lock-observer\')'),
        );
    }
}
