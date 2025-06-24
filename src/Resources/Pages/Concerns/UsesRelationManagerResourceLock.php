<?php

namespace Kenepa\ResourceLock\Resources\Pages\Concerns;

use Filament\Facades\Filament;
use Kenepa\ResourceLock\Models\ResourceLock;
use Livewire\Attributes\On;

trait UsesRelationManagerResourceLock
{
    use UsesLocks;

    public $relatedRecord;

    public string $parentClass;
    public string $relatedClass;

    public function bootUsesRelationManagerResourceLock(): void
    {
        $this->parentClass = get_class($this->getRelationship()->getParent());
        $this->relatedClass = get_class($this->getRelationship()->getRelated());
    }

    #[On('resourceLockObserver::unlock')]
    public function resourceLockObserverUnlock()
    {
        if ($this->relatedRecord) {
            if ($this->relatedRecord->unlock(force: true)) {
                $this->closeLockedResourceModal();
                $this->relatedLock();
            }
        } else {
            if ($this->getOwnerRecord()->unlock(force: true)) {
                $this->closeLockedResourceModal();
                $this->getOwnerRecord()->lock();
            }
        }
    }

    public function relatedLock()
    {
        $resourceLockModel = config('resource-lock.models.ResourceLock', ResourceLock::class);
        $guard = Filament::auth()->name;
        $resourceLock = new $resourceLockModel;
        $resourceLock->user_id = auth()->guard($guard)->user()->id;
        $resourceLock->lockable_id = $this->relatedRecord->id;
        $resourceLock->lockable_type = $this->relatedRecord->getMorphClass();
        $resourceLock->save();
    }

    public function mountTableAction(
        string $name,
        ?string $record = null,
        array $arguments = []
    ): mixed {
        parent::mountTableAction($name, $record);

        if ($name == 'edit') {
            $this->relatedRecord = $this->relatedClass::find($record);
            $this->checkIfResourceLockHasExpired($this->relatedRecord);
            $this->lockResource($this->relatedRecord);
        }

        return null;
    }

    public function unmountTableAction(
        bool $shouldCancelParentActions = true
    ): void {
        if ($this->mountedTableActionRecord) {
            $this->relatedRecord = $this->relatedClass::find(
                $this->mountedTableActionRecord
            );

            $this->relatedRecord->unlock();
        }

        parent::unmountTableAction($shouldCancelParentActions);
    }

    public function resourceLockReturnUrl()
    {
        $parentClassResource = Filament::getCurrentPanel()->getModelResource($this->getRelationship()->getParent());

        return $parentClassResource::getUrl('edit', ['record' => $this->getOwnerRecord()->id]);
    }

    public function getResourceLockOwner(): void
    {
        if (config('resource-lock.lock_notice.display_resource_lock_owner', false)) {
            $getResourceLockOwnerActionClass = config('resource-lock.actions.get_resource_lock_owner_action');
            $getResourceLockOwnerAction = app($getResourceLockOwnerActionClass);

            $this->resourceLockOwner = $getResourceLockOwnerAction->execute($this->relatedRecord->resourceLock->user);
        }
    }
}
