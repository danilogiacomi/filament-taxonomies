<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\ConceptResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConcept extends EditRecord
{
    protected static string $resource = ConceptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
