<?php

namespace App\Filament\Admin\Resources\GuildResource\Pages;

use App\Filament\Admin\Resources\GuildResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuild extends EditRecord
{
    protected static string $resource = GuildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}