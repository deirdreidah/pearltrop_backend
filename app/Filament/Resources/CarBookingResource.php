<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarBookingResource\Pages;
use App\Models\CarBooking;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components as Schemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class CarBookingResource extends Resource
{
    protected static ?string $model = CarBooking::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | \UnitEnum | null $navigationGroup = 'Fleet Management';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Schemas\Section::make('Booking Information')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'rent' => 'Car Rental',
                                'ride' => 'Ride Booking',
                            ])
                            ->default('rent')
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('car_id')
                            ->relationship('car', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required(),
                        Forms\Components\TextInput::make('total_price')
                            ->numeric()
                            ->prefix('UGX')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rent' => 'info',
                        'ride' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'rent' => 'Rental',
                        'ride' => 'Ride',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'info',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'rent' => 'Rentals',
                        'ride' => 'Rides',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\SelectFilter::make('car')
                    ->relationship('car', 'name'),
            ])
            ->actions([
                Actions\ViewAction::make()->iconButton()->tooltip('View Booking'),
                Actions\EditAction::make()->iconButton()->tooltip('Edit Booking'),
                Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete Booking')
                    ->requiresConfirmation()
                    ->action(function (CarBooking $record) {
                        $response = (new \App\Services\CarBookingService())->delete($record);
                        if (!$response->success) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error deleting booking')
                                ->body($response->message)
                                ->danger()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title("Booking #{$record->id} has been deleted successfully")
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
                        ->successNotificationTitle('Bookings have been deleted successfully'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarBookings::route('/'),
            'create' => Pages\CreateCarBooking::route('/create'),
            'edit' => Pages\EditCarBooking::route('/{record}/edit'),
        ];
    }
}
