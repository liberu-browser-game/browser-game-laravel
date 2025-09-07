<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Player;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPlayersTable extends BaseWidget
{
    protected static ?string $heading = 'Recent Players';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Player::query()->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('experience')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Joined'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Player $record): string => route('filament.admin.resources.players.view', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}