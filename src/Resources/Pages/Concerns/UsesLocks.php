<?php

namespace Kenepa\ResourceLock\Resources\Pages\Concerns;

trait UsesLocks
{
    public ?string $resourceLockOwner = null;

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

        if ($record->isLockedByCurrentUser()) {
            // Do Nothing
        } elseif ($record->isLocked()) {
            $this->openLockedResourceModal();
        } else {
            $record->lock();
        }
    }

    public function resourceLockReturnUrl()
    {
        return $this->getResource()::getUrl('index');
    }

    /*
    * Inside the resource lock observer blade component is a modal that contains the actions that
    * a user can take when they are greeted by one of these modals.
    * This is filament native modal that is called.
    * Learn more: https://github.com/filamentphp/filament/discussions/3419
    */
    protected function openLockedResourceModal(): void
    {
        $this->getResourceLockOwner();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'resourceIsLockedNotice',
            'returnUrl' => $this->resourceLockReturnUrl(),
            'resourceLockOwner' => $this->resourceLockOwner,
        ]);
    }

    protected function closeLockedResourceModal(): void
    {
        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'resourceIsLockedNotice',
        ]);
    }
}
