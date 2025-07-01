<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\Pages;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;

class TaxonomyResource extends Resource
{
    protected static ?string $model = Taxonomy::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Textarea::make('description')->columnSpanFull(),
                Select::make('state')
                    ->options(TaxonomyStates::options())
                    ->required(),
                Select::make('type')
                    ->options(TaxonomyTypes::options())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')->limit(50),
                TextColumn::make('state'),
                TextColumn::make('type'),
                TextColumn::make('terms_count')->counts('terms'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->button(),
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
             TaxonomyResource\RelationManagers\TermsRelationManager::class,
         ];
     }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxonomies::route('/'),
            'create' => Pages\CreateTaxonomy::route('/create'),
            'edit' => Pages\EditTaxonomy::route('/{record}/edit'),
        ];
    }
}
