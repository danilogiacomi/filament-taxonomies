<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Net7\FilamentTaxonomies\Filament\Resources\ConceptResource;
use Net7\FilamentTaxonomies\Models\Concept;

class ConceptsRelationManager extends RelationManager
{
    protected static string $relationship = 'concepts';

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                ConceptResource::getFormSchema()
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                
                // one alternative
                // Stack::make(
                //     [
                //     TextColumn::make('label'),
                //     TextColumn::make('uri'),
                // ])

                // another alternative
                // Split::make(
                // [
                //     TextColumn::make('label'),
                //     TextColumn::make('uri'),
                // ])
                // ->from('md')

                // maybe the best one, TODO: TBD
                TextColumn::make('label')
                ->description(fn (Concept $record): string => $record->uri)


                ])
                ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order_column')
            ->defaultSort('order_column');
    
            ;
    }
}
