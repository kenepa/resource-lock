<?php

namespace Kenepa\ResourceLock\Models\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Kenepa\ResourceLock\Models\ResourceLock;

/*
 * The HasLocks trait provides several function to models to handle locking and locking of records.
 */

trait HasLocks
{
    public function resourceLock(): MorphOne
    {
        return $this->morphOne(config('resource-lock.models.ResourceLock', ResourceLock::class), 'lockable');
    }

    /*
     * This function returns true if locking the resource was successful
     */
    public function lock(): bool
    {
        if (! $this->isLocked()) {
            $resourceLock = new ResourceLock;
            $resourceLock->user_id = auth()->user()->id;
            $this->resourceLock()->save($resourceLock);

            return true;
        }

        return false;
    }

    public function isLockedByCurrentUser(): bool
    {
        $resourceLock = $this->resourceLock;

        if ($resourceLock && $resourceLock->user->id === auth()->user()->id) {
            return true;
        }

        return false;
    }

    public function isLocked(): bool
    {
        if (is_null($this->resourceLock)) {
            return false;
        }

        return $this->resourceLock->exists();
    }

    public function hasExpiredLock(): bool
    {
        if (! $this->isLocked()) {
            return false;
        }

        $expiredDate = (new Carbon($this->resourceLock->updated_at))->addMinutes(config('resource-lock.locks_expires'));

        return Carbon::now()->greaterThan($expiredDate);
    }

    /*
     * This function returns true if unlocking the resource was successful
     */
    public function unlock(bool $force = false): bool
    {
        if ($this->isLocked()) {
            if ($force || $this->lockCreatedByCurrentUser() || $this->hasExpiredLock()) {
                $this->resourceLock()->delete();

                return true;
            }
        }

        return false;
    }

    public function lockCreatedByCurrentUser(): bool
    {
        return $this->resourceLock->user_id === auth()->user()->id;
    }
}
