<?php

namespace Kenepa\ResourceLock\Tests;

use Filament\FilamentServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Kenepa\ResourceLock\ResourceLockServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Kenepa\\ResourceLock\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        config()->set('resource-lock.models.User', '\Kenepa\ResourceLock\Tests\Resources\Models\User');

        $migration = include __DIR__ . '/../database/migrations/create_resource_lock_table.php.stub';
        $migration->up();

        $migration = include __DIR__ . '/Migrations/post_migration.php';
        $migration->up();

        $migration = include __DIR__ . '/Migrations/user_migration.php';
        $migration->up();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            ResourceLockServiceProvider::class,
        ];
    }
}
