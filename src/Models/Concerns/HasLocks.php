<?php

namespace Kenepa\ResourceLock\Models\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Kenepa\ResourceLock\Models\ResourceLock;

/**
 * The HasLocks trait provides several functions to models to handle locking and unlocking of records.
 */
trait HasLocks
{
    /**
     * Get the morphOne relationship for the ResourceLock model.
     */
    public function resourceLock(): MorphOne
    {
        return $this->morphOne(config('resource-lock.models.ResourceLock', ResourceLock::class), 'lockable');
    }

    /**
     * Lock the resource.
     *
     * @return bool Returns true if locking the resource was successful, false otherwise.
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

    /**
     * Check if the resource is locked by the current user.
     *
     * @return bool Returns true if the resource is locked by the current user, false otherwise.
     */
    public function isLockedByCurrentUser(): bool
    {
        $resourceLock = $this->resourceLock;

        if ($resourceLock && $resourceLock->user->id === auth()->user()->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if the resource is locked.
     *
     * @return bool Returns true if the resource is locked, false otherwise.
     */
    public function isLocked(): bool
    {
        if (is_null($this->resourceLock)) {
            return false;
        }

        return $this->resourceLock->exists();
    }

    /**
     * Check if the lock on the resource has expired.
     *
     * @return bool Returns true if the lock on the resource has expired, false otherwise.
     */
    public function hasExpiredLock(): bool
    {
        if (! $this->isLocked()) {
            return false;
        }

        $expiredDate = (new Carbon($this->resourceLock->updated_at))->addMinutes(config('resource-lock.locks_expires'));

        return Carbon::now()->greaterThan($expiredDate);
    }

    /**
     * Unlock the resource.
     *
     * @param  bool  $force Whether to force unlock or not.
     * @return bool Returns true if unlocking the resource was successful, false otherwise.
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

    /**
     * Check if the lock was created by the current user.
     *
     * @return bool Returns true if the lock was created by the current user, false otherwise.
     */
    public function lockCreatedByCurrentUser(): bool
    {
        return $this->resourceLock->user_id === auth()->user()->id;
    }
}
