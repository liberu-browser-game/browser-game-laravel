<?php

namespace App\Filament\Admin\Resources;

use App\Models\Player_Item;
use App\Models\Player;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\PlayerItemResource\Pages;
use App\Filament\Admin\Resources\PlayerItemResource\RelationManagers;

class PlayerItemResource extends Resource
{
    protected static ?string $model = Player_Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Game Management';

    protected static ?string $navigationLabel = 'Player Items';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('player_id')
                    ->relationship('player', 'username')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Player'),
                Forms\Components\Select::make('item_id')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Item'),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('player.username')
                    ->searchable()
                    ->sortable()
                    ->label('Player'),
                Tables\Columns\TextColumn::make('item.name')
                    ->searchable()
                    ->sortable()
                    ->label('Item'),
                Tables\Columns\TextColumn::make('item.type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'weapon' => 'danger',
                        'armor' => 'info',
                        'consumable' => 'success',
                        'material' => 'warning',
                        'quest' => 'primary',
                        default => 'gray',
                    })
                    ->label('Item Type'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
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
                Tables\Filters\SelectFilter::make('player')
                    ->relationship('player', 'username')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('item')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('item_type')
                    ->options([
                        'weapon' => 'Weapon',
                        'armor' => 'Armor',
                        'consumable' => 'Consumable',
                        'material' => 'Material',
                        'quest' => 'Quest Item',
                        'misc' => 'Miscellaneous',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('item', fn (Builder $query) => $query->where('type', $value))
                        );
                    }),
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
            'index' => Pages\ListPlayerItems::route('/'),
            'create' => Pages\CreatePlayerItem::route('/create'),
            'view' => Pages\ViewPlayerItem::route('/{record}'),
            'edit' => Pages\EditPlayerItem::route('/{record}/edit'),
        ];
    }
}