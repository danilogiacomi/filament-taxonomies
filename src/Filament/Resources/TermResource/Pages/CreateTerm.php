<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TermResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\TermResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTerm extends CreateRecord
{
    protected static string $resource = TermResource::class;

    protected function getRedirectUrl(): string
    {
        return  $this->getResource()::getUrl('index');
    }
}
