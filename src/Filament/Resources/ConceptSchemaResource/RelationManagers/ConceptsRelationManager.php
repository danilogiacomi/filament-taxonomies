<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Net7\FilamentTaxonomies\Filament\Resources\ConceptResource;

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
                TextColumn::make('label'),
                TextColumn::make('uri'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
