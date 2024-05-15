<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\Pages;
use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\RelationManagers;
use Net7\FilamentTaxonomies\Models\ConceptSchema;
use Filament\Forms;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Net7\FilamentTaxonomies\Enums\ConceptSchemaStates;
use Net7\FilamentTaxonomies\Enums\ConceptSchemaTypes;
use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\RelationManagers\ConceptsRelationManager;

class ConceptSchemaResource extends Resource
{
    protected static ?string $model = ConceptSchema::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('label')->required(),
                // TextInput::make('title'),
                Section::make('Data')
                ->schema([
                    Fieldset::make('data')
                ->label('')
                ->schema([
                    Textarea::make('description')->columnSpanFull(),
                    Select::make('state')
                        ->options(ConceptSchemaStates::options())
                        ->required(),
                    Select::make('type')
                        ->options(ConceptSchemaTypes::options())
                        ->required(),
                    TextInput::make('owner'),
                    TextInput::make('uri')->required()->url(),
                    TextInput::make('creator'),
                    TextInput::make('license'),
                ])])
                ->collapsible()
                ->collapsed(function (string $operation) {
                    if ($operation == 'edit') return true;
                })

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label'),
                TextColumn::make('state'),
                TextColumn::make('type'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('jsonLd')
                    ->label('jsonLD')
                    ->url(function ($record){
                        return route('filament-taxonomies-taxonomy', ['schema' => $record->label]);
                    })
                    ->openUrlInNewTab()
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
            ConceptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConceptSchemas::route('/'),
            'create' => Pages\CreateConceptSchema::route('/create'),
            'edit' => Pages\EditConceptSchema::route('/{record}/edit'),
        ];
    }
}
