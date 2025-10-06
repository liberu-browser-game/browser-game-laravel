<?php

namespace App\Filament\Admin\Resources\PlayerItemResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\PlayerItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerItem extends EditRecord
{
    protected static string $resource = PlayerItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}