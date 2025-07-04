<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TermResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Net7\FilamentTaxonomies\Filament\Resources\TermResource;

class CreateTerm extends CreateRecord
{
    protected static string $resource = TermResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
