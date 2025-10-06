<?php

namespace App\Filament\Admin\Resources\SiteSettingsResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\SiteSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSiteSettings extends ViewRecord
{
    protected static string $resource = SiteSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}