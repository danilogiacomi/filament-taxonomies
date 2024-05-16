<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptResource\Pages;
use Net7\FilamentTaxonomies\Filament\Resources\ConceptResource\RelationManagers;
use Net7\FilamentTaxonomies\Models\Concept;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConceptResource extends Resource
{
    protected static ?string $model = Concept::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function getFormSchema(){
        return [

            TextInput::make('label')->required()->columnSpanFull(),
            Textarea::make('definition')->columnSpanFull(),
            TextInput::make('uri')->required()->url()->columnSpanFull(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label'),
                TextColumn::make('uri'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListConcepts::route('/'),
            'create' => Pages\CreateConcept::route('/create'),
            'edit' => Pages\EditConcept::route('/{record}/edit'),
        ];
    }
}
