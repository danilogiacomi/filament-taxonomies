<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Net7\FilamentTaxonomies\Filament\Resources\TermResource\Pages;
use Net7\FilamentTaxonomies\Models\Term;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static bool $shouldRegisterNavigation = false;

    public static function getFormSchema(){
        return [];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ListTerms::route('/'),
            'create' => Pages\CreateTerm::route('/create'),
            'edit' => Pages\EditTerm::route('/{record}/edit'),
        ];
    }
}
