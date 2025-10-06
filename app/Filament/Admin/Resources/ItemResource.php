<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\ItemResource\Pages\ListItems;
use App\Filament\Admin\Resources\ItemResource\Pages\CreateItem;
use App\Filament\Admin\Resources\ItemResource\Pages\ViewItem;
use App\Filament\Admin\Resources\ItemResource\Pages\EditItem;
use App\Models\Item;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\ItemResource\Pages;
use App\Filament\Admin\Resources\ItemResource\RelationManagers;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static string | \UnitEnum | null $navigationGroup = 'Game Management';

    protected static ?string $navigationLabel = 'Items';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(1000)
                    ->rows(4),
                Select::make('type')
                    ->options([
                        'weapon' => 'Weapon',
                        'armor' => 'Armor',
                        'consumable' => 'Consumable',
                        'material' => 'Material',
                        'quest' => 'Quest Item',
                        'misc' => 'Miscellaneous',
                    ])
                    ->required(),
                Select::make('rarity')
                    ->options([
                        'common' => 'Common',
                        'uncommon' => 'Uncommon',
                        'rare' => 'Rare',
                        'epic' => 'Epic',
                        'legendary' => 'Legendary',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'weapon' => 'danger',
                        'armor' => 'info',
                        'consumable' => 'success',
                        'material' => 'warning',
                        'quest' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('rarity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'common' => 'gray',
                        'uncommon' => 'success',
                        'rare' => 'info',
                        'epic' => 'warning',
                        'legendary' => 'danger',
                        default => 'gray',
                    })
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
                SelectFilter::make('type')
                    ->options([
                        'weapon' => 'Weapon',
                        'armor' => 'Armor',
                        'consumable' => 'Consumable',
                        'material' => 'Material',
                        'quest' => 'Quest Item',
                        'misc' => 'Miscellaneous',
                    ]),
                SelectFilter::make('rarity')
                    ->options([
                        'common' => 'Common',
                        'uncommon' => 'Uncommon',
                        'rare' => 'Rare',
                        'epic' => 'Epic',
                        'legendary' => 'Legendary',
                    ]),
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
            'index' => ListItems::route('/'),
            'create' => CreateItem::route('/create'),
            'view' => ViewItem::route('/{record}'),
            'edit' => EditItem::route('/{record}/edit'),
        ];
    }
}