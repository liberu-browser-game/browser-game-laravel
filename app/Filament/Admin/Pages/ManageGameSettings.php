<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Settings\GameSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;

class ManageGameSettings extends SettingsPage
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $settings = GameSettings::class;

    protected static string | UnitEnum | null $navigationGroup = 'System';

    protected static ?string $title = 'Game Settings';

    protected static ?string $navigationLabel = 'Game Settings';

    protected static ?int $navigationSort = 1;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Player Settings')
                    ->description('Configure default player settings and progression')
                    ->schema([
                        Toggle::make('player_registration_enabled')
                            ->label('Enable Player Registration')
                            ->helperText('Allow new players to register for the game')
                            ->default(true)
                            ->inline(false),
                        TextInput::make('starting_level')
                            ->label('Starting Level')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->helperText('The level new players start at'),
                        TextInput::make('max_player_level')
                            ->label('Maximum Player Level')
                            ->numeric()
                            ->default(100)
                            ->minValue(1)
                            ->required()
                            ->helperText('The highest level a player can reach'),
                        TextInput::make('starting_gold')
                            ->label('Starting Gold')
                            ->numeric()
                            ->default(100)
                            ->minValue(0)
                            ->required()
                            ->helperText('Amount of gold new players start with'),
                        TextInput::make('starting_experience')
                            ->label('Starting Experience')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->helperText('Amount of experience new players start with'),
                    ])
                    ->columns(2),

                Section::make('Game Mechanics')
                    ->description('Configure core game mechanics and multipliers')
                    ->schema([
                        TextInput::make('experience_multiplier')
                            ->label('Experience Multiplier')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required()
                            ->helperText('Multiply all experience gains by this value'),
                        TextInput::make('gold_multiplier')
                            ->label('Gold Multiplier')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required()
                            ->helperText('Multiply all gold gains by this value'),
                        TextInput::make('max_inventory_size')
                            ->label('Maximum Inventory Size')
                            ->numeric()
                            ->default(100)
                            ->minValue(10)
                            ->required()
                            ->helperText('Maximum number of items a player can carry'),
                        TextInput::make('max_active_quests')
                            ->label('Maximum Active Quests')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->required()
                            ->helperText('Maximum number of quests a player can have active'),
                    ])
                    ->columns(2),

                Section::make('Features')
                    ->description('Enable or disable game features')
                    ->schema([
                        Toggle::make('pvp_enabled')
                            ->label('Enable PvP (Player vs Player)')
                            ->helperText('Allow players to fight each other')
                            ->default(false)
                            ->inline(false),
                        Toggle::make('guild_system_enabled')
                            ->label('Enable Guild System')
                            ->helperText('Allow players to create and join guilds')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }
}
