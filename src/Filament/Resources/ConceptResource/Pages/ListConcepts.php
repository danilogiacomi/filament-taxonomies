<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\ConceptResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Net7\FilamentTaxonomies\Models\Concept;

class ListConcepts extends ListRecords
{
    protected static string $resource = ConceptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            // Concept::class
            
        ];
    }
}
