<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxonomy extends CreateRecord
{
    protected static string $resource = TaxonomyResource::class;

    protected function getRedirectUrl(): string
    {
        return  $this->getResource()::getUrl('index');
    }
}

