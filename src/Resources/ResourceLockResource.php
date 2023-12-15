<?php

namespace Kenepa\ResourceLock\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Kenepa\ResourceLock\Models\ResourceLock;
use Kenepa\ResourceLock\Resources\ResourceLockResource\ManageResourceLocks;

class ResourceLockResource extends Resource
{
    /**
     * @return string|null
     */
    public static function getNavigationIcon(): ?string
    {
        return __(config('resource-lock.manager.navigation_icon', 'heroicon-o-lock-closed'));
    }

    public static function getModel(): string
    {
        return config('resource-lock.models.ResourceLock', ResourceLock::class);
    }

    public static function getPluralLabel(): string
    {
        return __(config('resource-lock.manager.plural_label', 'Resource Locks'));
    }
    
    public static function form(Form $form): Form
    {
        return $form
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
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-lock-open')
                    ->successNotificationTitle(__('resource-lock::manager.unlocked'))
                    ->label(__('resource-lock::manager.unlock')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
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
        if (config('resource-lock.manager.limited_access')) {
            return Gate::allows(config('resource-lock.manager.gate'));
        }

        return true;
    }

    public static function canDeleteAny(): bool
    {
        if (config('resource-lock.manager.limited_access')) {
            return Gate::allows(config('resource-lock.manager.gate'));
        }

        return true;
    }

    public static function getNavigationBadge(): ?string
    {
        if (! config('resource-lock.manager.navigation_badge')) {
            return null;
        }

        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return config('resource-lock.manager.navigation_label', 'Resource Lock Manager');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('resource-lock.manager.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('resource-lock.manager.navigation_sort');
    }
}
