<?php

namespace Kenepa\ResourceLock\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Kenepa\ResourceLock\Models\ResourceLock;

class ResourceLockClearCommand extends Command
{
    protected $signature = 'resource-lock:clear {--force : Force clear without confirmation}';

    protected $description = 'Clear all resource locks from the database';

    public function handle(): void
    {
        if (! $this->option('force') && ! $this->confirm('Are you sure you want to clear all resource locks? This action cannot be undone.')) {
            $this->info('Operation cancelled.');

            return;
        }

        try {
            $count = ResourceLock::count();

            if ($count === 0) {
                $this->info('No resource locks found to clear.');

                return;
            }

            $this->info("Removing {$count} resource lock(s)...");
            ResourceLock::truncate();
            $this->info('All resource locks successfully removed.');
        } catch (Exception $e) {
            $this->error('Failed to clear resource locks: ' . $e->getMessage());

            return;
        }
    }
}
