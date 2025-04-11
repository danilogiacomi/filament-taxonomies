<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConceptSchemas extends ListRecords
{
    protected static string $resource = ConceptSchemaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
