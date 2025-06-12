<?php

use Kenepa\ResourceLock\Models\ResourceLock;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

describe('Resource Locking', function () {
    it('can lock a resource', function () {
        // Arrange
        $user = createUser();
        actingAs($user);
        $post = createPost();

        // Act
        $post->lock();
        $post->refresh();

        // Assert
        expect($post->resourceLock->lockable_id)
            ->toBe($post->id)
            ->and($post->resourceLock->user_id)
            ->toBe($user->id);
        assertDatabaseCount(ResourceLock::class, 1);
        expect($post->isLockedByCurrentUser())->toBeTrue();
        expect($post->isLocked())->toBeTrue();
    });
});

describe('Resource Unlocking', function () {
    it('can unlock a resource', function () {
        // Arrange
        $user = createUser();
        actingAs($user);
        $post = createPost();
        $post->lock();

        // Act
        $post->refresh();
        $post->unlock();
        $post->refresh();

        // Assert
        expect($post->resourceLock)->toBeNull();
        assertDatabaseCount(ResourceLock::class, 0);
        expect($post->isLockedByCurrentUser())->toBeFalse();
        expect($post->isLocked())->toBeFalse();
    });

    it('can unlock a resource by force', function () {
        // Arrange
        $user = createUser();
        actingAs($user);
        $post = createPost();
        $post->lock();
        $admin = createUser();
        actingAs($admin);

        // Act
        $post->refresh();
        $forceLockResult = $post->unlock(force: true);
        $post->refresh();

        // Assert
        assertDatabaseCount(ResourceLock::class, 0);
        expect($post->resourceLock)->toBeNull();
        expect($forceLockResult)->toBeTrue();
    });
});

describe('Lock Status Checks', function () {
    it('can check if a lock has been expired', function () {
        // Arrange
        $user = createUser();
        actingAs($user);
        $post = createPost();
        createExpiredResourceLock($user, $post);

        // Act
        // (No explicit act step, as the check is the assertion)

        // Assert
        expect($post->hasExpiredLock())->toBeTrue();
    });
});

describe('Lock Timestamp Updates', function () {
    it('updates timestamp when lock is refreshed by current user', function () {
        // Arrange
        $user = createUser();
        actingAs($user);
        $post = createPost();

        $post->lock();
        $post->refresh();
        $initialTimestamp = $post->resourceLock->updated_at;

        // Act
        sleep(1);
        $result = $post->lock();
        $post->refresh();

        // Assert
        expect($result)->toBeTrue();
        expect($post->resourceLock->updated_at)->toBeGreaterThan($initialTimestamp);
        assertDatabaseCount(ResourceLock::class, 1);
    });
});
