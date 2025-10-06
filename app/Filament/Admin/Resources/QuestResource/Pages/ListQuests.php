<?php

namespace App\Filament\Admin\Resources\QuestResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\QuestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuests extends ListRecords
{
    protected static string $resource = QuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}