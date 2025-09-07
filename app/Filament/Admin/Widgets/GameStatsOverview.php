<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Player;
use App\Models\Guild;
use App\Models\Item;
use App\Models\Quest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GameStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Players', Player::count())
                ->description('Registered players')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Active Guilds', Guild::count())
                ->description('Total guilds')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('info'),
            Stat::make('Items Available', Item::count())
                ->description('Total items in game')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),
            Stat::make('Quests Available', Quest::count())
                ->description('Total quests')
                ->descriptionIcon('heroicon-m-map')
                ->color('primary'),
        ];
    }
}