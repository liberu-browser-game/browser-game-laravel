<?php

namespace App\Filament\Admin\Resources\PlayerItemResource\Pages;

use App\Filament\Admin\Resources\PlayerItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerItem extends EditRecord
{
    protected static string $resource = PlayerItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}