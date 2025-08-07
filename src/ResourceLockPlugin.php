<?php

namespace Kenepa\ResourceLock;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
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

    protected ?bool $unlockerLimitedAccess = null;

    protected ?string $unlockerGate = null;

    protected ?string $resourceClass = null;

    protected ?object $userModel = null;

    protected ?object $resourceLockModel = null;

    protected ?int $lockTimeout = null;

    protected ?bool $checkLocksBeforeSaving = null;

    protected ?string $resourceLockOwnerAction = null;

    protected bool $usesPollingToDetectPresence = false;

    protected int $presencePollingInterval = 15;

    protected bool $pollingKeepAlive = false;

    protected bool $pollingVisible = false;

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
            PanelsRenderHook::PAGE_START,
            fn (): string => Blade::render('@livewire(\'resource-lock-observer\')'),
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

    public function resourceClass(?string $class): static
    {
        $this->resourceClass = $class;

        return $this;
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass ?? config('resource-lock.resource.class', LockResource::class);
    }

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

    public function lockTimeout(?int $seconds): static
    {
        $this->lockTimeout = $seconds;

        return $this;
    }

    public function getLockTimeout(): int
    {
        return $this->lockTimeout ?? config('resource-lock.lock_timeout', 600);
    }

    public function checkLocksBeforeSaving(bool $check = true): static
    {
        $this->checkLocksBeforeSaving = $check;

        return $this;
    }

    public function shouldCheckLocksBeforeSaving(): bool
    {
        return $this->checkLocksBeforeSaving ?? config('resource-lock.check_locks_before_saving', true);
    }

    public function resourceLockOwnerAction(?string $action): static
    {
        $this->resourceLockOwnerAction = $action;

        return $this;
    }

    public function getResourceLockOwnerAction(): string
    {
        return $this->resourceLockOwnerAction ?? config('resource-lock.actions.get_resource_lock_owner_action', \Kenepa\ResourceLock\Actions\GetResourceLockOwnerAction::class);
    }

    public function usesPollingToDetectPresence(bool $enable = true): static
    {
        $this->usesPollingToDetectPresence = $enable;

        return $this;
    }

    public function shouldUsePollingToDetectPresence(): bool
    {
        return $this->usesPollingToDetectPresence ?? false;
    }

    public function presencePollingInterval(int $seconds): static
    {
        $this->presencePollingInterval = $seconds;

        return $this;
    }

    public function getPresencePollingInterval(): int
    {
        return $this->presencePollingInterval ?? 15;
    }

    public function pollingKeepAlive(bool $keepAlive = true): static
    {
        $this->pollingKeepAlive = $keepAlive;

        return $this;
    }

    public function shouldUsePollingKeepAlive(): bool
    {
        return $this->pollingKeepAlive ?? false;
    }

    public function pollingVisible(bool $visible = true): static
    {
        $this->pollingVisible = $visible;

        return $this;
    }

    public function shouldUsePollingVisible(): bool
    {
        return $this->pollingVisible ?? false;
    }
}
