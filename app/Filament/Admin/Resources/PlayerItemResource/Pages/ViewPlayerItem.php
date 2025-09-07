<?php

namespace App\Filament\Admin\Resources\PlayerItemResource\Pages;

use App\Filament\Admin\Resources\PlayerItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayerItem extends ViewRecord
{
    protected static string $resource = PlayerItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}