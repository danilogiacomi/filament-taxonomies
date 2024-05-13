<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Widgets\ConceptWidget;

class EditConceptSchema extends EditRecord
{
    protected static string $resource = ConceptSchemaResource::class;

    protected function getRedirectUrl(): string
    {
        return  $this->getResource()::getUrl('index');
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ConceptWidget::class
        ];
    }
}
