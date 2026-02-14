<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\PlayerResource\Pages\ListPlayers;
use App\Filament\Admin\Resources\PlayerResource\Pages\CreatePlayer;
use App\Filament\Admin\Resources\PlayerResource\Pages\ViewPlayer;
use App\Filament\Admin\Resources\PlayerResource\Pages\EditPlayer;
use App\Filament\Admin\Resources\PlayerResource\RelationManagers\ItemsRelationManager;
use App\Filament\Admin\Resources\PlayerResource\RelationManagers\QuestsRelationManager;
use App\Filament\Admin\Resources\PlayerResource\RelationManagers\ResourcesRelationManager;
use App\Models\Player;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\PlayerResource\Pages;
use App\Filament\Admin\Resources\PlayerResource\RelationManagers;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Game Management';

    protected static ?string $navigationLabel = 'Players';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->description('Basic player account details')
                    ->schema([
                        TextInput::make('username')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique username for the player'),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Player\'s email address'),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                            ->helperText('Leave blank to keep current password'),
                    ])
                    ->columns(2),
                
                Section::make('Game Stats')
                    ->description('Player game progression and statistics')
                    ->schema([
                        TextInput::make('level')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(100)
                            ->helperText('Current player level (1-100)'),
                        TextInput::make('experience')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Total experience points'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('level')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('experience')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('high_level')
                    ->query(fn (Builder $query): Builder => $query->where('level', '>=', 10))
                    ->label('High Level Players (10+)'),
                SelectFilter::make('level_range')
                    ->label('Level Range')
                    ->options([
                        '1-10' => 'Beginner (1-10)',
                        '11-25' => 'Intermediate (11-25)',
                        '26-50' => 'Advanced (26-50)',
                        '51-100' => 'Expert (51-100)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value'] ?? null) {
                            '1-10' => $query->whereBetween('level', [1, 10]),
                            '11-25' => $query->whereBetween('level', [11, 25]),
                            '26-50' => $query->whereBetween('level', [26, 50]),
                            '51-100' => $query->whereBetween('level', [51, 100]),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            QuestsRelationManager::class,
            ResourcesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayers::route('/'),
            'create' => CreatePlayer::route('/create'),
            'view' => ViewPlayer::route('/{record}'),
            'edit' => EditPlayer::route('/{record}/edit'),
        ];
    }
}