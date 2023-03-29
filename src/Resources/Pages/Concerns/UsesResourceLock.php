<?php

namespace Kenepa\ResourceLock\Resources\Pages\Concerns;

trait UsesResourceLock
{

    private bool $isLockable = true;

//    public string $resourceLockType;

    public function bootUsesResourceLock(): void
    {
        $this->listeners = array_merge($this->listeners, [
            'resourceLockObserver::init' => 'resourceLockObserverInit',
            'resourceLockObserver::unload' => 'resourceLockObserverUnload',
            'resourceLockObserver::unlock' => 'resourceLockObserverUnlock'
        ]);
    }

    public function lockResource()
    {
        $this->resourceLockType = class_basename($this->record);

        if ($this->record->isLockedByCurrentUser()) {
            // Do Nothing
        } else if ($this->record->isLocked()) {
            $this->openLockedResourceModal();
        } else {
            $this->record->lock();
        }
    }

    public function checkIfResourceLockHasExpired(): void
    {
        if ($this->record->hasExpiredLock()) {
            $this->record->unlock();
        }
    }

    protected function openLockedResourceModal(): void
    {
        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'resourceIsLockedNotice',
        ]);
    }

    protected function closeLockedResourceModal(): void
    {
        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'resourceIsLockedNotice',
        ]);
    }

    public function resourceLockObserverInit()
    {
        $this->checkIfResourceLockHasExpired();
        $this->lockResource();
    }

    public function resourceLockObserverUnload()
    {
        $this->record->unlock();
    }

    public function resourceLockObserverUnlock()
    {
        if ($this->record->unlock(force: true)) {
            $this->closeLockedResourceModal();
            $this->record->lock();
        }
    }

    public function save(bool $shouldRedirect = true): void
    {
        if (config('resource-lock.throw_forbidden_exception', true)) {
            abort_unless($this->record->isLocked() && $this->record->isLockedByCurrentUser(), 403);
        }
        
        parent::save($shouldRedirect);
    }
}