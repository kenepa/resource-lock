<?php

namespace Kenepa\ResourceLock\Resources\Pages\Concerns;

use Kenepa\ResourceLock\ResourceLockPlugin;

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
            'resourceLockObserver::unload' => 'resourceLockObserverUnload',
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
        if (ResourceLockPlugin::get()->shouldCheckLocksBeforeSaving()) {
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
        if (ResourceLockPlugin::get()->shouldDisplayResourceLockOwner()) {
            $getResourceLockOwnerActionClass = ResourceLockPlugin::get()->getResourceLockOwnerAction();
            $getResourceLockOwnerAction = app($getResourceLockOwnerActionClass);

            $this->resourceLockOwner = $getResourceLockOwnerAction->execute($this->resourceRecord->resourceLock->user);
        }
    }
}
