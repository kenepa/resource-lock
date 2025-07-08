<?php

namespace Kenepa\ResourceLock\Resources;

use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Kenepa\ResourceLock\ResourceLockPlugin;
use Kenepa\ResourceLock\Resources\LockResource\ManageResourceLocks;

class LockResource extends Resource
{
    public static function getNavigationIcon(): ?string
    {
        return ResourceLockPlugin::get()->getNavigationIcon();
    }

    public static function getModel(): string
    {
        return ResourceLockPlugin::get()->getResourceLockModel();
    }

    public static function getPluralLabel(): string
    {
        return ResourceLockPlugin::get()->getPluralLabel();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('Lock ID')),
                TextColumn::make('user.id')->label(__('User ID')),
                TextColumn::make('lockable.id')->label(__('Lockable ID')),
                TextColumn::make('lockable_type')->label(__('Lockable type')),
                TextColumn::make('created_at')->label(__('Created at')),
                TextColumn::make('updated_at')->label(__('Updated at')),
                TextColumn::make('updated_at')->label(__('Expired'))
                    ->badge()
                    ->color(static function ($record): string {
                        if ($record->isExpired()) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->icon(static function ($record): string {
                        if ($record->isExpired()) {
                            return 'heroicon-o-lock-open';
                        }

                        return 'heroicon-o-lock-closed';
                    })->formatStateUsing(static function ($record) {
                        if ($record->isExpired()) {
                            return __('resource-lock::manager.expired');
                        }

                        return __('resource-lock::manager.active');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                DeleteAction::make()
                    ->icon('heroicon-o-lock-open')
                    ->successNotificationTitle(__('resource-lock::manager.unlocked'))
                    ->label(__('resource-lock::manager.unlock')),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->icon('heroicon-o-lock-open')
                    ->successNotificationTitle(__('resource-lock::manager.unlocked_selected'))
                    ->label(__('resource-lock::manager.unlock')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageResourceLocks::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        if (ResourceLockPlugin::get()->shouldLimitAccessToResourceLockManager()) {
            return Gate::allows(ResourceLockPlugin::get()->getGate());
        }

        return true;
    }

    public static function canDeleteAny(): bool
    {
        if (ResourceLockPlugin::get()->shouldLimitAccessToResourceLockManager()) {
            return Gate::allows(ResourceLockPlugin::get()->getGate());
        }

        return true;
    }

    public static function getNavigationBadge(): ?string
    {
        if (! ResourceLockPlugin::get()->shouldShowNavigationBadge()) {
            return null;
        }

        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return ResourceLockPlugin::get()->getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return ResourceLockPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return ResourceLockPlugin::get()->getNavigationSort();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ResourceLockPlugin::get()->shouldRegisterNavigation();
    }
}
