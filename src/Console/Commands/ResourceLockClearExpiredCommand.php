<?php

namespace Kenepa\ResourceLock\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Kenepa\ResourceLock\Models\ResourceLock;

class ResourceLockClearExpiredCommand extends Command
{
    protected $signature = 'resource-lock:clear-expired {--force : Force clear without confirmation}';

    protected $description = 'Clear only expired resource locks from the database';

    public function handle(): void
    {
        if (! $this->option('force') && ! $this->confirm('Are you sure you want to clear all expired resource locks? This action cannot be undone.')) {
            $this->info('Operation cancelled.');

            return;
        }

        try {
            $expiredLocks = ResourceLock::all()->filter(function ($lock) {
                return $lock->isExpired();
            });

            $count = $expiredLocks->count();

            if ($count === 0) {
                $this->info('No expired resource locks found to clear.');

                return;
            }

            $this->info("Removing {$count} expired resource lock(s)...");

            foreach ($expiredLocks as $lock) {
                $lock->delete();
            }

            $this->info('All expired resource locks successfully removed.');
        } catch (Exception $e) {
            $this->error('Failed to clear expired resource locks: ' . $e->getMessage());

            return;
        }
    }
}
