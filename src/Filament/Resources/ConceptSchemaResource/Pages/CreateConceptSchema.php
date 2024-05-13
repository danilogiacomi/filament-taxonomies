<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateConceptSchema extends CreateRecord
{
    protected static string $resource = ConceptSchemaResource::class;

    protected function getRedirectUrl(): string
    {
        return  $this->getResource()::getUrl('index');
    }
}

