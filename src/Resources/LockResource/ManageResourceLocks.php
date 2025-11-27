<?php

namespace Kenepa\ResourceLock\Resources\LockResource;

use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Kenepa\ResourceLock\ResourceLockPlugin;
use Kenepa\ResourceLock\Resources\LockResource;

class ManageResourceLocks extends ManageRecords
{
    public static function getResource(): string
    {
        return app(ResourceLockPlugin::class)->getResourceClass();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make(__('resource-lock::manager.unlock_all'))
                ->label(__('resource-lock::manager.unlock_all'))
                ->icon('heroicon-o-lock-open')
                ->action(fn () => ResourceLockPlugin::get()->getResourceLockModel()::truncate())
                ->requiresConfirmation(),
        ];
    }
}
