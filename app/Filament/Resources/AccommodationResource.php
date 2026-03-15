<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccommodationResource\Pages;
use App\Models\Accommodation;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components as Schemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class AccommodationResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home-modern';

    protected static string | \UnitEnum | null $navigationGroup = 'Destination Management';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Schemas\Section::make('Accommodation Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options([
                                'Nature' => 'Nature',
                                'Cultural' => 'Cultural',
                                'Urban' => 'Urban',
                                'Adventure' => 'Adventure',
                            ]),
                        Forms\Components\FileUpload::make('image_path')
                            ->image()
                            ->directory('accommodations')
                            ->label('Image')
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
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Nature' => 'Nature',
                        'Cultural' => 'Cultural',
                        'Urban' => 'Urban',
                        'Adventure' => 'Adventure',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make()->iconButton()->tooltip('View Accommodation'),
                Actions\EditAction::make()->iconButton()->tooltip('Edit Accommodation'),
                Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete Accommodation')
                    ->requiresConfirmation()
                    ->action(function (\App\Models\Accommodation $record) {
                        $response = (new \App\Services\AccommodationService())->delete($record);
                        if (!$response->success) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error deleting accommodation')
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
                        ->successNotificationTitle('Accommodations have been deleted successfully'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccommodations::route('/'),
            'create' => Pages\CreateAccommodation::route('/create'),
            'edit' => Pages\EditAccommodation::route('/{record}/edit'),
        ];
    }
}
