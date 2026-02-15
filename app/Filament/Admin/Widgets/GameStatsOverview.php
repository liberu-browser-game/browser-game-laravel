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
                ->color('success')
                ->extraAttributes([
                    'class' => 'game-stat-card',
                ]),
            Stat::make('Active Guilds', Guild::count())
                ->description('Total guilds')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('info')
                ->extraAttributes([
                    'class' => 'game-stat-card',
                ]),
            Stat::make('Items Available', Item::count())
                ->description('Total items in game')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'game-stat-card',
                ]),
            Stat::make('Quests Available', Quest::count())
                ->description('Total quests')
                ->descriptionIcon('heroicon-m-map')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'game-stat-card',
                ]),
        ];
    }
    
    protected function getColumns(): int
    {
        // 2 columns on mobile, 4 on desktop
        return 2;
    }
}