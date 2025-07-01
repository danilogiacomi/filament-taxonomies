<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TermResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\TermResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Net7\FilamentTaxonomies\Models\Term;

class ListTerms extends ListRecords
{
    protected static string $resource = TermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            // Concept::class
            
        ];
    }
}
