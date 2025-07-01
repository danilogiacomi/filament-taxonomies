<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaxonomies extends ListRecords
{
    protected static string $resource = TaxonomyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


}
