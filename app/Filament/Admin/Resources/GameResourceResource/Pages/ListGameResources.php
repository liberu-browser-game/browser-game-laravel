<?php

namespace App\Filament\Admin\Resources\GameResourceResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\GameResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameResources extends ListRecords
{
    protected static string $resource = GameResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}