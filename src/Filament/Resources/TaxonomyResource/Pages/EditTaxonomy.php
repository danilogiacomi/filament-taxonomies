<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;


class EditTaxonomy extends EditRecord
{
    protected static string $resource = TaxonomyResource::class;

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
            // ConceptWidget::class
        ];
    }
}
