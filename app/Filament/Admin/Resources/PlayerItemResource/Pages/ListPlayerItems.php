<?php

namespace App\Filament\Admin\Resources\PlayerItemResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\PlayerItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlayerItems extends ListRecords
{
    protected static string $resource = PlayerItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}