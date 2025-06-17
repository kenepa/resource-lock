<?php

namespace Kenepa\ResourceLock\Resources\Pages\Concerns;

/**
 * This trait provides common methods used by both UsesResourceLock and
 * UsesSimpleResourceLock traits, offering core functionality for managing
 * resource locks.
 */
trait UsesLocks
{
    public ?string $resourceLockOwner = null;
    public ?string $resourceType = null;

    public function initializeResourceLock($record): void
    {
        if ($record->isUnlocked()) {
            $record->lock();

            return;
        }

        if ($record->hasExpiredLock()) {
            $record->unlock();
            $record->lock();

            return;
        }

        // Refresh the lock if it is locked by the current user
        if ($record->isLockedByCurrentUser()) {
            $record->lock();

            return;
        }

        // Locked by another user and not expired
        $this->openLockedResourceModal();
    }

    public function checkIfResourceLockHasExpired($record): void
    {
        if ($record->hasExpiredLock()) {
            $record->unlock();
        }
    }

    /*
    * This function handles the locking of a resource. It first performs several checks before a resource
    * is locked. This function is trigger after the resource lock observer has been initialized.
    */
    public function lockResource($record)
    {
        $this->resourceLockType = class_basename($record);

        if ($this->isLockedByOtherUser($record)) {
            $this->openLockedResourceModal();
        } else {
            $record->lock();
        }
    }

    public function isLockedByOtherUser($record): bool
    {
        return $record->isLocked() && ! $record->isLockedByCurrentUser();
    }

    public function resourceLockReturnUrl()
    {
        return $this->getResource()::getUrl('index');
    }

    public function setupPolling()
    {
        $this->dispatch('enablePollingInResourceLockObserver');
    }

    public function disablePolling()
    {
        $this->dispatch('disablePollingInResourceLockObserver');
    }

    public function renewLock()
    {
        $record = $this->record ?? $this->resourceRecord;

        if (! $record) {
            return;
        }

        if ($record->isUnlocked()) {
            $record->lock();

            return;
        }

        if ($record->isLockedByCurrentUser()) {
            $record->lock(); // Refresh/extend the lock
        } else {
            $this->openLockedResourceModal();
        }
    }

    /*
    * Inside the resource lock observer blade component is a modal that contains the actions that
    * a user can take when they are greeted by one of these modals.
    * This is filament native modal that is called.
    * Learn more: https://github.com/filamentphp/filament/discussions/3419
    */
    protected function openLockedResourceModal(): void
    {
        $record = $this->record ?? $this->resourceRecord;

        if (! $record) {
            return;
        }

        $this->getResourceLockOwner();

        $this->dispatch(
            'open-modal',
            id: 'resourceIsLockedNotice',
            returnUrl: $this->resourceLockReturnUrl(),
            resourceLockOwner: $this->resourceLockOwner
        );
    }

    protected function closeLockedResourceModal(): void
    {
        $this->dispatch(
            'close-modal',
            id: 'resourceIsLockedNotice'
        );
    }
}
