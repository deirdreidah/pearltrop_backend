<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Models\Car;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components as Schemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static string | \UnitEnum | null $navigationGroup = 'Fleet Management';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Schemas\Section::make('Car Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('brand')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->required(),
                        Forms\Components\ColorPicker::make('color'),
                    ])->columns(2),

                Schemas\Section::make('Pricing & Availability')
                    ->schema([
                        Forms\Components\TextInput::make('price_per_day')
                            ->numeric()
                            ->prefix('UGX')
                            ->required(),
                        Forms\Components\Toggle::make('is_available')
                            ->default(true),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('cars')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->money('UGX')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability'),
                Tables\Filters\SelectFilter::make('brand')
                    ->options(fn () => Car::query()->distinct()->pluck('brand', 'brand')->toArray())
                    ->searchable(),
            ])
            ->actions([
                Actions\Action::make('toggleAvailability')
                    ->label(fn (Car $record): string => $record->is_available ? 'Make Unavailable' : 'Make Available')
                    ->icon(fn (Car $record): string => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Car $record): string => $record->is_available ? 'danger' : 'success')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalHeading(fn (Car $record): string => $record->is_available ? 'Mark Car as Unavailable' : 'Mark Car as Available')
                    ->modalDescription('Are you sure you want to change the availability status of this car?')
                    ->action(fn (Car $record) => $record->update(['is_available' => !$record->is_available]))
                    ->tooltip(fn (Car $record): string => $record->is_available ? 'Make Unavailable' : 'Make Available'),
                Actions\ViewAction::make()->iconButton()->tooltip('View Car'),
                Actions\EditAction::make()->iconButton()->tooltip('Edit Car'),
                Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete Car')
                    ->requiresConfirmation()
                    ->action(function (Car $record) {
                        $response = (new \App\Services\CarService())->delete($record);
                        if (!$response->success) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error deleting car')
                                ->body($response->message)
                                ->danger()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title("{$record->name} has been deleted successfully")
                                ->success()
                                ->send();
                        }
                    })
                    ->successNotification(null),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->successNotificationTitle('Cars have been deleted successfully'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
