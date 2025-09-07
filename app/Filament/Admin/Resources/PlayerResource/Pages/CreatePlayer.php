<?php

namespace App\Filament\Admin\Resources\PlayerResource\Pages;

use App\Filament\Admin\Resources\PlayerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePlayer extends CreateRecord
{
    protected static string $resource = PlayerResource::class;
}