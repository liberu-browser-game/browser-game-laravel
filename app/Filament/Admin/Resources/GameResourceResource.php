<?php

namespace App\Filament\Admin\Resources;

use App\Models\Resource as GameResource;
use App\Models\Player;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\GameResourceResource\Pages;
use App\Filament\Admin\Resources\GameResourceResource\RelationManagers;

class GameResourceResource extends Resource
{
    protected static ?string $model = GameResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Game Management';

    protected static ?string $navigationLabel = 'Player Resources';

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
                Forms\Components\Select::make('resource_type')
                    ->options([
                        'gold' => 'Gold',
                        'wood' => 'Wood',
                        'stone' => 'Stone',
                        'iron' => 'Iron',
                        'food' => 'Food',
                        'energy' => 'Energy',
                        'gems' => 'Gems',
                    ])
                    ->required()
                    ->label('Resource Type'),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
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
                Tables\Columns\TextColumn::make('resource_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gold' => 'warning',
                        'wood' => 'success',
                        'stone' => 'gray',
                        'iron' => 'info',
                        'food' => 'primary',
                        'energy' => 'danger',
                        'gems' => 'purple',
                        default => 'gray',
                    })
                    ->sortable()
                    ->label('Resource Type'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state)),
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
                Tables\Filters\SelectFilter::make('resource_type')
                    ->options([
                        'gold' => 'Gold',
                        'wood' => 'Wood',
                        'stone' => 'Stone',
                        'iron' => 'Iron',
                        'food' => 'Food',
                        'energy' => 'Energy',
                        'gems' => 'Gems',
                    ]),
                Tables\Filters\SelectFilter::make('player')
                    ->relationship('player', 'username')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListGameResources::route('/'),
            'create' => Pages\CreateGameResource::route('/create'),
            'view' => Pages\ViewGameResource::route('/{record}'),
            'edit' => Pages\EditGameResource::route('/{record}/edit'),
        ];
    }
}