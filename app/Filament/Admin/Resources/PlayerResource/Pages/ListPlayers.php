<?php

namespace App\Filament\Admin\Resources\PlayerResource\Pages;

use App\Filament\Admin\Resources\PlayerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlayers extends ListRecords
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}