<?php

namespace App\Filament\Admin\Resources;

use App\Models\SiteSettings;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Site Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Settings')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Site Name'),
                        Forms\Components\TextInput::make('currency')
                            ->maxLength(10)
                            ->label('Currency Symbol'),
                        Forms\Components\TextInput::make('default_language')
                            ->maxLength(10)
                            ->default('en')
                            ->label('Default Language'),
                    ]),
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->maxLength(500)
                            ->rows(3),
                        Forms\Components\TextInput::make('country')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_01')
                            ->tel()
                            ->maxLength(20)
                            ->label('Primary Phone'),
                        Forms\Components\TextInput::make('phone_02')
                            ->tel()
                            ->maxLength(20)
                            ->label('Secondary Phone'),
                        Forms\Components\TextInput::make('phone_03')
                            ->tel()
                            ->maxLength(20)
                            ->label('Tertiary Phone'),
                    ]),
                Forms\Components\Section::make('Social Media')
                    ->schema([
                        Forms\Components\TextInput::make('facebook')
                            ->url()
                            ->maxLength(255)
                            ->label('Facebook URL'),
                        Forms\Components\TextInput::make('twitter')
                            ->url()
                            ->maxLength(255)
                            ->label('Twitter URL'),
                        Forms\Components\TextInput::make('github')
                            ->url()
                            ->maxLength(255)
                            ->label('GitHub URL'),
                        Forms\Components\TextInput::make('youtube')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Site Name'),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('default_language')
                    ->searchable()
                    ->sortable()
                    ->label('Language'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSettings::route('/create'),
            'view' => Pages\ViewSiteSettings::route('/{record}'),
            'edit' => Pages\EditSiteSettings::route('/{record}/edit'),
        ];
    }
}