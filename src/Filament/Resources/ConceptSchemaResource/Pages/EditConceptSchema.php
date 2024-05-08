<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConceptSchema extends EditRecord
{
    protected static string $resource = ConceptSchemaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
