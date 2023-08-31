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
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    public static function getModel(): string
    {
        return config('resource-lock.models.ResourceLock', ResourceLock::class);
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
                TextColumn::make('id')->label('Lock ID'),
                TextColumn::make('user.id')->label('User ID'),
                TextColumn::make('lockable.id')->label('Lockable ID'),
                TextColumn::make('lockable_type'),
                TextColumn::make('created_at'),
                TextColumn::make('updated_at'),
                TextColumn::make('updated_at')->label('Expired')
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
