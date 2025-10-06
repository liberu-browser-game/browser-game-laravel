<?php

namespace App\Filament\Admin\Resources\QuestResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\QuestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuest extends ViewRecord
{
    protected static string $resource = QuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}