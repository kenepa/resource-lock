<?php

namespace Kenepa\ResourceLock;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Kenepa\ResourceLock\Actions\GetResourceLockOwnerAction;
use Kenepa\ResourceLock\Models\ResourceLock;
use Kenepa\ResourceLock\Resources\LockResource;
use Livewire\Livewire;

class ResourceLockPlugin implements Plugin
{
    protected ?bool $displayResourceLockOwner = null;

    protected ?bool $navigationBadge = null;

    protected ?string $navigationIcon = null;

    protected ?string $navigationLabel = null;

    protected ?string $pluralLabel = null;

    protected ?string $navigationGroup = null;

    protected ?int $navigationSort = null;

    protected ?bool $limitedAccessToResourceLockManager = null;

    protected ?string $gate = null;

    protected ?bool $shouldRegisterNavigation = null;

    // Unlocker configuration
    protected ?bool $unlockerLimitedAccess = null;

    protected ?string $unlockerGate = null;

    // Resource configuration
    protected ?string $resourceClass = null;

    // Models configuration
    protected ?string $userModel = null;

    protected ?string $resourceLockModel = null;

    // Lock timeout configuration
    protected ?int $lockTimeout = null;

    // Check locks before saving
    protected ?bool $checkLocksBeforeSaving = null;

    // Actions configuration
    protected ?string $resourceLockOwnerAction = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'resource-lock';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                $this->getResourceClass(),
            ]);
    }

    public function boot(Panel $panel): void
    {
        Livewire::component('resource-lock-observer', Http\Livewire\ResourceLockObserver::class);

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn(): string => Blade::render('@livewire(\'resource-lock-observer\')'),
        );
    }

    public function displayResourceLockOwner(bool $display = true): static
    {
        $this->displayResourceLockOwner = $display;

        return $this;
    }

    public function shouldDisplayResourceLockOwner(): bool
    {
        return $this->displayResourceLockOwner ?? config('resource-lock.lock_notice.display_resource_lock_owner', true);
    }

    public function navigationBadge(bool $show = true): static
    {
        $this->navigationBadge = $show;

        return $this;
    }

    public function shouldShowNavigationBadge(): bool
    {
        return $this->navigationBadge ?? config('resource-lock.manager.navigation_badge', false);
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon ?? config('resource-lock.manager.navigation_icon', 'heroicon-o-lock-closed');
    }

    public function navigationLabel(?string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): string
    {
        return __($this->navigationLabel ?? config('resource-lock.manager.navigation_label', 'Resource Lock Manager'));
    }

    public function pluralLabel(?string $label): static
    {
        $this->pluralLabel = $label;

        return $this;
    }

    public function getPluralLabel(): string
    {
        return __($this->pluralLabel ?? config('resource-lock.manager.plural_label', 'Resource Locks'));
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? config('resource-lock.manager.navigation_group');
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort ?? config('resource-lock.manager.navigation_sort');
    }

    public function limitedAccessToResourceLockManager(bool $limited = true): static
    {
        $this->limitedAccessToResourceLockManager = $limited;

        return $this;
    }

    public function shouldLimitAccessToResourceLockManager(): bool
    {
        return $this->limitedAccessToResourceLockManager ?? config('resource-lock.manager.limited_access', false);
    }

    public function gate(?string $gate): static
    {
        $this->gate = $gate;

        return $this;
    }

    public function getGate(): ?string
    {
        return $this->gate ?? config('resource-lock.manager.gate', null);
    }

    public function registerNavigation(bool $register = true): static
    {
        $this->shouldRegisterNavigation = $register;

        return $this;
    }

    public function shouldRegisterNavigation(): bool
    {
        return $this->shouldRegisterNavigation ?? config('resource-lock.manager.should_register_navigation', true);
    }

    // Unlocker configuration methods
    public function unlockerLimitedAccess(bool $limited = true): static
    {
        $this->unlockerLimitedAccess = $limited;

        return $this;
    }

    public function shouldLimitUnlockerAccess(): bool
    {
        return $this->unlockerLimitedAccess ?? config('resource-lock.unlocker.limited_access', false);
    }

    public function unlockerGate(?string $gate): static
    {
        $this->unlockerGate = $gate;

        return $this;
    }

    public function getUnlockerGate(): ?string
    {
        return $this->unlockerGate ?? config('resource-lock.unlocker.gate', null);
    }

    // Resource class configuration
    public function resourceClass(?string $class): static
    {
        $this->resourceClass = $class;

        return $this;
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass ?? config('resource-lock.resource.class', LockResource::class);
    }

    // Models configuration
    public function userModel(?string $model): static
    {
        $this->userModel = $model;

        return $this;
    }

    public function getUserModel(): string
    {
        return $this->userModel ?? config('resource-lock.models.User', 'App\\Models\\User');
    }

    public function resourceLockModel(?string $model): static
    {
        $this->resourceLockModel = $model;

        return $this;
    }

    public function getResourceLockModel(): string
    {
        return $this->resourceLockModel ?? config('resource-lock.models.ResourceLock', ResourceLock::class);
    }

    // Lock timeout configuration
    public function lockTimeout(?int $minutes): static
    {
        $this->lockTimeout = $minutes;

        return $this;
    }

    public function getLockTimeout(): int
    {
        return $this->lockTimeout ?? config('resource-lock.lock_timeout', 10);
    }

    // Check locks before saving configuration
    public function checkLocksBeforeSaving(bool $check = true): static
    {
        $this->checkLocksBeforeSaving = $check;

        return $this;
    }

    public function shouldCheckLocksBeforeSaving(): bool
    {
        return $this->checkLocksBeforeSaving ?? config('resource-lock.check_locks_before_saving', true);
    }

    // Actions configuration
    public function resourceLockOwnerAction(?string $action): static
    {
        $this->resourceLockOwnerAction = $action;

        return $this;
    }

    public function getResourceLockOwnerAction(): string
    {
        return $this->resourceLockOwnerAction ?? config('resource-lock.actions.get_resource_lock_owner_action', \Kenepa\ResourceLock\Actions\GetResourceLockOwnerAction::class);
    }
}
