<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\QuestResource\Pages\ListQuests;
use App\Filament\Admin\Resources\QuestResource\Pages\CreateQuest;
use App\Filament\Admin\Resources\QuestResource\Pages\ViewQuest;
use App\Filament\Admin\Resources\QuestResource\Pages\EditQuest;
use App\Models\Quest;
use App\Models\Item;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\QuestResource\Pages;
use App\Filament\Admin\Resources\QuestResource\RelationManagers;

class QuestResource extends Resource
{
    protected static ?string $model = Quest::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';

    protected static string | \UnitEnum | null $navigationGroup = 'Game Management';

    protected static ?string $navigationLabel = 'Quests';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->maxLength(1000)
                    ->rows(4),
                TextInput::make('experience_reward')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->label('Experience Reward'),
                Select::make('item_reward_id')
                    ->relationship('itemReward', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Item Reward')
                    ->placeholder('Select an item reward (optional)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('experience_reward')
                    ->numeric()
                    ->sortable()
                    ->label('XP Reward'),
                TextColumn::make('itemReward.name')
                    ->label('Item Reward')
                    ->placeholder('None')
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
                Filter::make('has_item_reward')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('item_reward_id'))
                    ->label('Has Item Reward'),
                Filter::make('high_xp')
                    ->query(fn (Builder $query): Builder => $query->where('experience_reward', '>=', 100))
                    ->label('High XP Reward (100+)'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuests::route('/'),
            'create' => CreateQuest::route('/create'),
            'view' => ViewQuest::route('/{record}'),
            'edit' => EditQuest::route('/{record}/edit'),
        ];
    }
}