<?php

namespace App\Filament\Admin\Resources\ItemResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}