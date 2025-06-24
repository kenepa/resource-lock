<?php

namespace Kenepa\ResourceLock\Resources\Pages\Concerns;

/*
 * The Resource Lock Trait provides several functions to an Edit Resource page to lock & unlock resources.
 * Beware that you model needs to also use the App\Models\Concerns\ResourceLock concern.
 */

trait UsesResourceLock
{
    use UsesLocks;

    public string $returnUrl;

    public string $resourceLockType;

    private bool $isLockable = true;

    /*
     * Initializes livewire event listeners on boot. This function uses livewire lifecycle hooks
     * to hook into lifecycle events of the livewire component that uses this trait
     * learn more: https://laravel-livewire.com/docs/2.x/traits
     */
    public function bootUsesResourceLock(): void
    {
        $this->listeners = array_merge($this->listeners, [
            'resourceLockObserver::init' => 'resourceLockObserverInit',
            'resourceLockObserver::unload' => 'resourceLockObserverUnload',
            'resourceLockObserver::unlock' => 'resourceLockObserverUnlock',
        ]);
    }

    /*
     * This function is triggered when the resource lock observer component has been loaded.
     * The resource lock observer is a livewire component that triggers function based
     * on certain states and events that are happening on the page.
     */
    public function resourceLockObserverInit()
    {
        $this->returnUrl = $this->getResource()::getUrl('index');
        $this->checkIfResourceLockHasExpired($this->record);
        $this->lockResource($this->record);
    }

    public function resourceLockObserverUnload()
    {
        $this->record->unlock();
    }

    /*
     * Depending on your configuration is possible to unlock resource through the modal that is
     * presented to the users. This action is seen as forced unlock and will replace any lock
     * That is currently in place for that specific resource. Hone this power with care.
     */
    public function resourceLockObserverUnlock()
    {
        if (is_null($this->activeRelationManager)) {
            if ($this->record->unlock(force: true)) {
                $this->closeLockedResourceModal();
                $this->record->refresh();
                $this->record->lock();
            }
        }
    }

    /*
     * In any case the user is able to bypass the modal we also check if that user is allowed
     * to make any changes based on the resource lock that is currently in place.
     * This is just an extra fail-safe, but can be turnoff in the config file.
     */
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        if (config('resource-lock.check_locks_before_saving', true)) {
            $this->record->refresh();
            if ($this->record->isLocked() && ! $this->record->isLockedByCurrentUser()) {
                $this->checkIfResourceLockHasExpired($this->record);
                $this->lockResource($this->record);

                return;
            }
        }

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    public function getResourceLockOwner(): void
    {
        if (config('resource-lock.lock_notice.display_resource_lock_owner', false)) {
            $getResourceLockOwnerActionClass = config('resource-lock.actions.get_resource_lock_owner_action');
            $getResourceLockOwnerAction = app($getResourceLockOwnerActionClass);

            $this->resourceLockOwner = $getResourceLockOwnerAction->execute($this->record->resourceLock->user);
        }
    }
}
