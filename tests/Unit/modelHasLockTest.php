<?php

use Kenepa\ResourceLock\Models\ResourceLock;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

it('can lock a resource', function () {
    $user = createUser();
    actingAs($user);
    $post = createPost();

    $post->lock();
    $post->refresh();

    expect($post->resourceLock->lockable_id)
        ->toBe($post->id)
            ->and($post->resourceLock->user_id)
            ->toBe($user->id);
    assertDatabaseCount(ResourceLock::class, 1);

    expect($post->isLockedByCurrentUser())->toBeTrue();
    expect($post->isLocked())->toBeTrue();
});

it('can unlock a resource', function () {
    $user = createUser();
    actingAs($user);
    $post = createPost();
    $post->lock();

    $post->refresh();
    $post->unlock();
    $post->refresh();

    expect($post->resourceLock)->toBeNull();
    assertDatabaseCount(ResourceLock::class, 0);
    expect($post->isLockedByCurrentUser())->toBeFalse();
    expect($post->isLocked())->toBeFalse();
});

it('can unlock a resource by force', function () {
    $user = createUser();
    actingAs($user);
    $post = createPost();
    $post->lock();
    $admin = createUser();
    actingAs($admin);

    $post->refresh();
    $forceLockResult = $post->unlock(force: true);
    $post->refresh();

    assertDatabaseCount(ResourceLock::class, 0);
    expect($post->resourceLock)->toBeNull();
    expect($forceLockResult)->toBeTrue();
});

it('can check if a lock has been expired', function () {
    $user = createUser();
    actingAs($user);
    $post = createPost();
    createExpiredResourceLock($user, $post);

    expect($post->hasExpiredLock())->toBeTrue();
});
