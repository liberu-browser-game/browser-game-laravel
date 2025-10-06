<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\SiteSettingsResource\Pages\ListSiteSettings;
use App\Filament\Admin\Resources\SiteSettingsResource\Pages\CreateSiteSettings;
use App\Filament\Admin\Resources\SiteSettingsResource\Pages\ViewSiteSettings;
use App\Filament\Admin\Resources\SiteSettingsResource\Pages\EditSiteSettings;
use App\Models\SiteSettings;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\SiteSettingsResource\Pages;
use App\Filament\Admin\Resources\SiteSettingsResource\RelationManagers;

class SiteSettingsResource extends Resource
{
    protected static ?string $model = SiteSettings::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Site Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Settings')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Site Name'),
                        TextInput::make('currency')
                            ->maxLength(10)
                            ->label('Currency Symbol'),
                        TextInput::make('default_language')
                            ->maxLength(10)
                            ->default('en')
                            ->label('Default Language'),
                    ]),
                Section::make('Contact Information')
                    ->schema([
                        Textarea::make('address')
                            ->maxLength(500)
                            ->rows(3),
                        TextInput::make('country')
                            ->maxLength(100),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone_01')
                            ->tel()
                            ->maxLength(20)
                            ->label('Primary Phone'),
                        TextInput::make('phone_02')
                            ->tel()
                            ->maxLength(20)
                            ->label('Secondary Phone'),
                        TextInput::make('phone_03')
                            ->tel()
                            ->maxLength(20)
                            ->label('Tertiary Phone'),
                    ]),
                Section::make('Social Media')
                    ->schema([
                        TextInput::make('facebook')
                            ->url()
                            ->maxLength(255)
                            ->label('Facebook URL'),
                        TextInput::make('twitter')
                            ->url()
                            ->maxLength(255)
                            ->label('Twitter URL'),
                        TextInput::make('github')
                            ->url()
                            ->maxLength(255)
                            ->label('GitHub URL'),
                        TextInput::make('youtube')
                            ->url()
                            ->maxLength(255)
                            ->label('YouTube URL'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Site Name'),
                TextColumn::make('currency')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('default_language')
                    ->searchable()
                    ->sortable()
                    ->label('Language'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteSettings::route('/'),
            'create' => CreateSiteSettings::route('/create'),
            'view' => ViewSiteSettings::route('/{record}'),
            'edit' => EditSiteSettings::route('/{record}/edit'),
        ];
    }
}