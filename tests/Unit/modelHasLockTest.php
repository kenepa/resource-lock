<?php

use Illuminate\Support\Carbon;
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

it('detects lock when another user tries to edit a locked resource', function () {
    // Arrange
    $user1 = createUser();
    $post = createPost();

    actingAs($user1);
    $post->lock();

    $user2 = createUser();
    actingAs($user2);

    // Act & Assert
    $post->refresh();
    expect($post->isLocked())->toBeTrue()
        ->and($post->isLockedByCurrentUser())->toBeFalse();
});

it('automatically considers locks expired after timeout period', function () {
    // Arrange
    $user = createUser();
    actingAs($user);
    $post = createPost();
    $post->lock();

    // Act
    ResourceLock::where('lockable_id', $post->id)->update([
        'updated_at' => Carbon::now()->subMinutes(30),
    ]);
    $post->refresh();

    // Assert
    expect($post->hasExpiredLock())->toBeTrue();
    expect($post->isLocked())->toBeFalse();
});

it('prevents unlocking by a different user without force', function () {
    // Arrange
    $user1 = createUser();
    actingAs($user1);
    $post = createPost();
    $post->lock();

    $user2 = createUser();
    actingAs($user2);

    // Act
    $post->refresh();
    $unlockResult = $post->unlock(force: false);
    $post->refresh();

    // Assert
    expect($unlockResult)->toBeFalse();
    expect($post->isLocked())->toBeTrue();
    assertDatabaseCount(ResourceLock::class, 1);
});

it('prevents locking a resource that is already locked by another user', function () {
    // Arrange
    $user1 = createUser();
    actingAs($user1);
    $post = createPost();
    $post->lock();

    $user2 = createUser();
    actingAs($user2);

    // Act
    $post->refresh();
    $lockResult = $post->lock();

    // Assert
    expect($lockResult)->toBeFalse();
    expect($post->isLocked())->toBeTrue();
    expect($post->isLockedByCurrentUser())->toBeFalse();
    assertDatabaseCount(ResourceLock::class, 1);
});

it('prevents multiple users from locking when expired locks exist', function () {
    // Arrange
    $user1 = createUser();
    $user2 = createUser();
    $post = createPost();

    // Create multiple expired locks for the same resource
    createExpiredResourceLock($user1, $post);
    createExpiredResourceLock($user2, $post);

    // Act & Assert with user1
    actingAs($user1);
    $post->refresh();
    expect($post->isUnlocked())
        ->toBeTrue()
        ->and($post->lock())
        ->toBeTrue(); // This should be true because locks are expired
    // User1 can lock because resource appears unlocked

    // Act & Assert with user2
    actingAs($user2);
    $post->refresh();
    expect($post->isUnlocked())
        ->toBeFalse()
        ->and($post->lock())
        ->toBeFalse();
});
