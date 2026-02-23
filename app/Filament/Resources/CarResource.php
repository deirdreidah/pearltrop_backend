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
                    ])->columns(2),

                Schemas\Section::make('Pricing & Availability')
                    ->schema([
                        Forms\Components\TextInput::make('price_per_day')
                            ->numeric()
                            ->prefix('$')
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
                Tables\Columns\TextColumn::make('price_per_day')
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_available'),
            ])
            ->actions([
                Actions\ViewAction::make()->iconButton()->tooltip('View Car'),
                Actions\EditAction::make()->iconButton()->tooltip('Edit Car'),
                Actions\DeleteAction::make()->iconButton()->tooltip('Delete Car'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
