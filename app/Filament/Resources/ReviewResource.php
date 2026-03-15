<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components as Schemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';

    protected static string | \UnitEnum | null $navigationGroup = 'Destination Management';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Schemas\Section::make('Review Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('accommodation_id')
                            ->relationship('accommodation', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->required(),
                        Forms\Components\Textarea::make('comment')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accommodation.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        '1' => '1 Star',
                        '2' => '2 Stars',
                        '3' => '3 Stars',
                        '4' => '4 Stars',
                        '5' => '5 Stars',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make()->iconButton()->tooltip('View Review'),
                Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete Review')
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $response = (new \App\Services\ReviewService())->delete($record);
                        if (!$response->success) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error deleting review')
                                ->body($response->message)
                                ->danger()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title("Review by {$record->user->name} has been deleted successfully")
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
                        ->successNotificationTitle('Reviews have been deleted successfully'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
        ];
    }
}
