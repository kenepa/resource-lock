<?php

namespace Kenepa\ResourceLock\Tests;

use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Kenepa\ResourceLock\ResourceLockServiceProvider;
use Kenepa\ResourceLock\Tests\Fixtures\AdminPanelProvider;
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
        config()->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');

        $migration = include __DIR__ . '/../database/migrations/create_resource_lock_table.php.stub';
        $migration->up();

        $migration = include __DIR__ . '/Migrations/post_migration.php';
        $migration->up();

        $migration = include __DIR__ . '/Migrations/user_migration.php';
        $migration->up();

        view()->addLocation(__DIR__ . '/Fixtures/views');
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            TablesServiceProvider::class,
            FilamentServiceProvider::class,
            WidgetsServiceProvider::class,
            ResourceLockServiceProvider::class,
            AdminPanelProvider::class,
        ];
    }
}
