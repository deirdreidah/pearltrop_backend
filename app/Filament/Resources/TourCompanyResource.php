<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TourCompanyResource\Pages;
use App\Models\TourCompany;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components as Schemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class TourCompanyResource extends Resource
{
    protected static ?string $model = TourCompany::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string | \UnitEnum | null $navigationGroup = 'Tour Management';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Schemas\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->required()
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Schemas\Section::make('Address & Brand')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('tour-companies'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\Textarea::make('address')
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
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Actions\ViewAction::make()->iconButton()->tooltip('View Tour Company'),
                Actions\EditAction::make()->iconButton()->tooltip('Edit Tour Company'),
                Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete Tour Company')
                    ->requiresConfirmation()
                    ->action(function (TourCompany $record) {
                        $response = (new \App\Services\TourCompanyService())->delete($record);
                        if (!$response->success) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error deleting tour company')
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
                        ->successNotificationTitle('Tour companies have been deleted successfully'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTourCompanies::route('/'),
            'create' => Pages\CreateTourCompany::route('/create'),
            'view' => Pages\ViewTourCompany::route('/{record}'),
            'edit' => Pages\EditTourCompany::route('/{record}/edit'),
        ];
    }
}
