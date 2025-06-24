<?php

namespace Kenepa\ResourceLock\Resources\Pages\Concerns;

trait UsesSimpleResourceLock
{
    use UsesLocks;

    public string $returnUrl;

    public $resourceRecord;

    public string $resourceLockType;

    private bool $isLockable = true;

    public function bootUsesSimpleResourceLock(): void
    {
        $this->listeners = array_merge($this->listeners, [
            'resourceLockObserver::init' => 'resourceLockObserverInit',
            'resourceLockObserver::unloadSimple' => 'resourceLockObserverUnload',
            'resourceLockObserver::unlock' => 'resourceLockObserverUnlock',
        ]);
    }

    public function mountTableAction(string $name, ?string $record = null, array $arguments = []): mixed
    {
        parent::mountTableAction($name, $record);
        $this->resourceRecord = $this->getMountedTableActionRecord();

        $this->returnUrl = $this->getResource()::getUrl('index');
        $this->checkIfResourceLockHasExpired($this->resourceRecord);
        $this->lockResource($this->resourceRecord);

        return null;
    }

    public function callMountedTableAction(array $arguments = []): mixed
    {
        if (config('resource-lock.check_locks_before_saving', true)) {
            $this->resourceRecord->refresh();
            if ($this->resourceRecord->isLocked() && ! $this->resourceRecord->isLockedByCurrentUser()) {
                $this->checkIfResourceLockHasExpired($this->resourceRecord);
                $this->lockResource($this->resourceRecord);

                return null;
            }
        }
        parent::callMountedTableAction($arguments);

        return null;
    }

    public function resourceLockObserverUnload()
    {
        $this->resourceRecord->unlock();
    }

    public function resourceLockObserverUnlock()
    {
        if ($this->resourceRecord->unlock(force: true)) {
            $this->closeLockedResourceModal();
            $this->resourceRecord->lock();
        }
    }

    public function getResourceLockOwner(): void
    {
        if (config('resource-lock.lock_notice.display_resource_lock_owner', false)) {
            $getResourceLockOwnerActionClass = config('resource-lock.actions.get_resource_lock_owner_action');
            $getResourceLockOwnerAction = app($getResourceLockOwnerActionClass);

            $this->resourceLockOwner = $getResourceLockOwnerAction->execute($this->resourceRecord->resourceLock->user);
        }
    }
}
