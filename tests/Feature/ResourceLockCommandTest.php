<?php

use Kenepa\ResourceLock\Models\ResourceLock;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

describe('ResourceLockClearCommand', function () {
    it('clears all resource locks with force flag', function () {
        // Arrange
        $user = createUser();
        $post1 = createPost();
        $post2 = createPost();

        // Create some locks
        createActiveResourceLock($user, $post1);
        createActiveResourceLock($user, $post2);

        assertDatabaseCount(ResourceLock::class, 2);

        // Act
        artisan('resource-lock:clear --force')
            ->expectsOutput('Removing 2 resource lock(s)...')
            ->expectsOutput('All resource locks successfully removed.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 0);
    });

    it('prompts for confirmation when force flag is not used', function () {
        // Arrange
        $user = createUser();
        $post = createPost();
        createActiveResourceLock($user, $post);

        assertDatabaseCount(ResourceLock::class, 1);

        // Act - user confirms
        artisan('resource-lock:clear')
            ->expectsConfirmation('Are you sure you want to clear all resource locks? This action cannot be undone.', 'yes')
            ->expectsOutput('Removing 1 resource lock(s)...')
            ->expectsOutput('All resource locks successfully removed.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 0);
    });

    it('cancels operation when user declines confirmation', function () {
        // Arrange
        $user = createUser();
        $post = createPost();
        createActiveResourceLock($user, $post);

        assertDatabaseCount(ResourceLock::class, 1);

        // Act - user declines
        artisan('resource-lock:clear')
            ->expectsConfirmation('Are you sure you want to clear all resource locks? This action cannot be undone.', 'no')
            ->expectsOutput('Operation cancelled.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 1);
    });

    it('displays message when no locks exist', function () {
        // Arrange
        assertDatabaseCount(ResourceLock::class, 0);

        // Act
        artisan('resource-lock:clear --force')
            ->expectsOutput('No resource locks found to clear.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 0);
    });
});

describe('ResourceLockClearExpiredCommand', function () {
    it('clears only expired resource locks with force flag', function () {
        // Arrange
        $user = createUser();
        $post1 = createPost();
        $post2 = createPost();
        $post3 = createPost();

        // Create expired locks
        createExpiredResourceLock($user, $post1);
        createExpiredResourceLock($user, $post2);

        // Create active lock
        createActiveResourceLock($user, $post3);

        assertDatabaseCount(ResourceLock::class, 3);

        // Act
        artisan('resource-lock:clear-expired --force')
            ->expectsOutput('Removing 2 expired resource lock(s)...')
            ->expectsOutput('All expired resource locks successfully removed.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 1);

        // Verify the remaining lock is the active one
        $remainingLock = ResourceLock::first();
        expect($remainingLock->lockable_id)->toBe($post3->id)
            ->and($remainingLock->isExpired())->toBeFalse();
    });

    it('prompts for confirmation when force flag is not used', function () {
        // Arrange
        $user = createUser();
        $post = createPost();
        createExpiredResourceLock($user, $post);

        assertDatabaseCount(ResourceLock::class, 1);

        // Act - user confirms
        artisan('resource-lock:clear-expired')
            ->expectsConfirmation('Are you sure you want to clear all expired resource locks? This action cannot be undone.', 'yes')
            ->expectsOutput('Removing 1 expired resource lock(s)...')
            ->expectsOutput('All expired resource locks successfully removed.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 0);
    });

    it('cancels operation when user declines confirmation', function () {
        // Arrange
        $user = createUser();
        $post = createPost();
        createExpiredResourceLock($user, $post);

        assertDatabaseCount(ResourceLock::class, 1);

        // Act - user declines
        artisan('resource-lock:clear-expired')
            ->expectsConfirmation('Are you sure you want to clear all expired resource locks? This action cannot be undone.', 'no')
            ->expectsOutput('Operation cancelled.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 1);
    });

    it('displays message when no expired locks exist', function () {
        // Arrange
        $user = createUser();
        $post = createPost();
        createActiveResourceLock($user, $post);

        assertDatabaseCount(ResourceLock::class, 1);

        // Act
        artisan('resource-lock:clear-expired --force')
            ->expectsOutput('No expired resource locks found to clear.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 1);
    });

    it('handles mixed expired and active locks correctly', function () {
        // Arrange
        $user1 = createUser();
        $user2 = createUser();
        $post1 = createPost();
        $post2 = createPost();
        $post3 = createPost();
        $post4 = createPost();

        // Create a mix of expired and active locks
        createExpiredResourceLock($user1, $post1);
        createActiveResourceLock($user1, $post2); // Active
        createExpiredResourceLock($user2, $post3);
        createActiveResourceLock($user2, $post4); // Active

        assertDatabaseCount(ResourceLock::class, 4);

        // Act
        artisan('resource-lock:clear-expired --force')
            ->expectsOutput('Removing 2 expired resource lock(s)...')
            ->expectsOutput('All expired resource locks successfully removed.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 2);

        // Verify only active locks remain
        $remainingLocks = ResourceLock::all();
        expect($remainingLocks->count())
            ->toBe(2)
            ->and($remainingLocks->every(fn ($lock) => ! $lock->isExpired())
            )->toBeTrue();
    });

    it('displays message when database is empty', function () {
        // Arrange
        assertDatabaseCount(ResourceLock::class, 0);

        // Act
        artisan('resource-lock:clear-expired --force')
            ->expectsOutput('No expired resource locks found to clear.')
            ->assertExitCode(0);

        // Assert
        assertDatabaseCount(ResourceLock::class, 0);
    });
});
