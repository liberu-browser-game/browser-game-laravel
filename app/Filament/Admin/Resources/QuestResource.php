<?php

namespace App\Filament\Admin\Resources;

use App\Models\Quest;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Game Management';

    protected static ?string $navigationLabel = 'Quests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(1000)
                    ->rows(4),
                Forms\Components\TextInput::make('experience_reward')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->label('Experience Reward'),
                Forms\Components\Select::make('item_reward_id')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('experience_reward')
                    ->numeric()
                    ->sortable()
                    ->label('XP Reward'),
                Tables\Columns\TextColumn::make('itemReward.name')
                    ->label('Item Reward')
                    ->placeholder('None')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_item_reward')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('item_reward_id'))
                    ->label('Has Item Reward'),
                Tables\Filters\Filter::make('high_xp')
                    ->query(fn (Builder $query): Builder => $query->where('experience_reward', '>=', 100))
                    ->label('High XP Reward (100+)'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListQuests::route('/'),
            'create' => Pages\CreateQuest::route('/create'),
            'view' => Pages\ViewQuest::route('/{record}'),
            'edit' => Pages\EditQuest::route('/{record}/edit'),
        ];
    }
}