<?php

namespace App\Filament\Admin\Resources\QuestResource\Pages;

use App\Filament\Admin\Resources\QuestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuest extends EditRecord
{
    protected static string $resource = QuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}