<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TermResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\TermResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTerm extends EditRecord
{
    protected static string $resource = TermResource::class;

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

}
