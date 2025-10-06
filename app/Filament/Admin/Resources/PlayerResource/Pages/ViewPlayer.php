<?php

namespace App\Filament\Admin\Resources\PlayerResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\PlayerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayer extends ViewRecord
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}