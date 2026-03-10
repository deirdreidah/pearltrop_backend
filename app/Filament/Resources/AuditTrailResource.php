<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditTrailResource\Pages;
use App\Helpers\PermissionHelper;
use App\Models\AuditTrail;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components as Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class AuditTrailResource extends Resource
{
    protected static ?string $model = AuditTrail::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | \UnitEnum | null $navigationGroup = 'System';
    protected static ?int $navigationSort = 29;
    protected static ?string $slug = 'audit-trails';


    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return PermissionHelper::can('delete', 'audit-trails');
    }

    public static function canViewAny(): bool
    {
        return PermissionHelper::can('view', 'audit-trails');
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Grid::make(3)
                    ->schema([
                        Schemas\Group::make([
                            Schemas\Section::make('General Information')
                                ->schema([
                                    Schemas\TextEntry::make('event_type')
                                        ->label('Action')
                                        ->badge()
                                        ->color(fn(string $state): string => match (strtolower($state)) {
                                            'created', 'create' => 'success',
                                            'updated', 'update' => 'warning',
                                            'deleted', 'delete' => 'danger',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn($state) => strtoupper($state)),
                                    Schemas\TextEntry::make('model_type')
                                        ->label('Model')
                                        ->formatStateUsing(fn($state) => class_basename($state)),
                                    Schemas\TextEntry::make('model_id')
                                        ->label('Record ID'),
                                    Schemas\TextEntry::make('user.name')
                                        ->label('Performed By')
                                        ->default('System'),
                                    Schemas\TextEntry::make('created_at')
                                        ->label('Date & Time')
                                        ->dateTime('F j, Y g:i A')
                                        ->description(fn($record) => $record->created_at->diffForHumans()),
                                ])->columns(1),
                        ])->columnSpan(1),

                        Schemas\Group::make([
                            Schemas\Section::make('Audit Details')
                                ->schema([
                                    Schemas\TextEntry::make('message')
                                        ->label('Summary')
                                        ->columnSpanFull(),
                                    Schemas\KeyValueEntry::make('changes_detailed')
                                        ->label('Field Changes')
                                        ->visible(fn($record) => !empty($record->changes))
                                        ->columnSpanFull(),
                                    Schemas\TextEntry::make('no_changes')
                                        ->label('Field Changes')
                                        ->default('No significant field changes recorded.')
                                        ->visible(fn($record) => empty($record->changes))
                                        ->columnSpanFull(),
                                ])->columns(1),

                            Schemas\Section::make('Technical Metadata')
                                ->schema([
                                    Schemas\TextEntry::make('ip_address')
                                        ->label('IP Address')
                                        ->icon('heroicon-o-computer-desktop'),
                                    Schemas\TextEntry::make('user_agent')
                                        ->label('User Agent')
                                        ->size(Schemas\TextEntry\TextEntrySize::Small)
                                        ->columnSpanFull(),
                                ])->columns(1)
                                ->collapsed(),
                        ])->columnSpan(2),
                    ]),

                Schemas\Section::make('Raw Data Partition')
                    ->schema([
                        Schemas\Grid::make(2)
                            ->schema([
                                Schemas\KeyValueEntry::make('old_values_formatted')
                                    ->label('Previous State')
                                    ->columnSpan(1),
                                Schemas\KeyValueEntry::make('new_values_formatted')
                                    ->label('New State')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('event_type')
                    ->label('Action')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'created', 'create' => 'success',
                        'updated', 'update' => 'warning',
                        'deleted', 'delete' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match (strtolower($state)) {
                        'created', 'create' => 'heroicon-o-plus-circle',
                        'updated', 'update' => 'heroicon-o-pencil',
                        'deleted', 'delete' => 'heroicon-o-trash',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->extraAttributes(['class' => 'font-bold']),

                Tables\Columns\TextColumn::make('model_type')
                    ->label('Model')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('model_id')
                    ->label('Model ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('message')
                    ->label('Record')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->message),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ?? 'System')
                    ->color('primary')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('changes_summary')
                    ->label('Changes')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($record) {
                        $changes = $record->changes ?? [];
                        if (empty($changes)) return 'No changes';
                        
                        $keys = array_keys($changes);
                        $count = count($keys);
                        $summary = implode(', ', array_slice($keys, 0, 3));
                        
                        if ($count > 3) {
                            $summary .= ' + ' . ($count - 3) . ' more';
                        }
                        
                        return $summary;
                    }),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->icon('heroicon-o-computer-desktop')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(25)
                    ->tooltip(fn($record) => $record->user_agent)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->description(fn($record) => $record->created_at->diffForHumans()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_type')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                    ])
                    ->label('Action Type')
                    ->multiple()
                    ->placeholder('All Actions'),

                Tables\Filters\SelectFilter::make('model_type')
                    ->options(function () {
                        return AuditTrail::distinct()
                            ->pluck('model_type')
                            ->mapWithKeys(fn($type) => [$type => class_basename($type)])
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->label('Model')
                    ->placeholder('All Models'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn($q) => $q->whereDate('created_at', '>=', $data['date_from']))
                            ->when($data['date_to'], fn($q) => $q->whereDate('created_at', '<=', $data['date_to']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'From: ' . \Carbon\Carbon::parse($data['date_from'])->format('M j, Y');
                        }
                        if ($data['date_to'] ?? null) {
                            $indicators[] = 'To: ' . \Carbon\Carbon::parse($data['date_to'])->format('M j, Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('User')
                    ->placeholder('All Users'),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label(false)
                    ->color('primary'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->deferFilters()
            ->persistFiltersInSession()
            ->striped()
            ->defaultPaginationPageOption(25)
            ->paginated([10, 25, 50, 100])
            ->headerActions([
                Actions\Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success'),
            ])
            ->emptyStateHeading('No audit trails found')
            ->emptyStateDescription('Once activities occur, they will appear here.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditTrails::route('/'),
            'view' => Pages\ViewAuditTrail::route('/{record}'),
        ];
    }

}
