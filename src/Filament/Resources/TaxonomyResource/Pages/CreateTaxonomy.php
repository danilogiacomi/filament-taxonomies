<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\Pages;

use Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxonomy extends CreateRecord
{
    protected static string $resource = TaxonomyResource::class;

    protected function getRedirectUrl(): string
    {
        return  $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('semantic_info')
                ->label('About Semantic Metadata')
                ->icon('heroicon-o-information-circle')
                ->color('gray')
                ->modalHeading('About Semantic Metadata')
                ->modalDescription('An internal URI will be automatically generated for this taxonomy based on its name after creation.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }
}

