<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource;

class EditTaxonomy extends EditRecord
{
    protected static string $resource = TaxonomyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('semantic_data')
                ->label('Manage Semantic Metadata')
                ->icon('heroicon-o-code-bracket')
                ->color('info')
                ->form([
                    TextInput::make('uri')
                        ->label('URI')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Auto-generated from taxonomy name'),
                ])
                ->fillForm(fn (): array => [
                    'uri' => $this->record->uri,
                ])
                ->action(function (array $data): void {
                    // URI is auto-generated, no manual update needed
                }),
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
