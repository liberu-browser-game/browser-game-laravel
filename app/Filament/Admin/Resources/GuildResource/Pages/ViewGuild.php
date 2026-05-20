<?php

namespace App\Filament\Admin\Resources\GuildResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\GuildResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGuild extends ViewRecord
{
    protected static string $resource = GuildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}