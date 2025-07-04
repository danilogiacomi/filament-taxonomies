<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TermResource\Pages;

use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Net7\FilamentTaxonomies\Enums\UriTypes;
use Net7\FilamentTaxonomies\Filament\Resources\TermResource;

class EditTerm extends EditRecord
{
    protected static string $resource = TermResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('semantic_data')
                ->label('Manage Semantic Data')
                ->icon('heroicon-o-code-bracket')
                ->color('info')
                ->form([
                    Toggle::make('is_external_uri')
                        ->label('Use External URI')
                        ->helperText('Enable to define a custom external URI instead of auto-generated internal URI')
                        ->live()
                        ->columnSpanFull(),
                    TextInput::make('uri')
                        ->label('External URI')
                        ->required()
                        ->url()
                        ->columnSpanFull()
                        ->visible(fn (Forms\Get $get) => $get('is_external_uri'))
                        ->helperText('Must not use the same domain as this application')
                        ->rules([
                            function (Forms\Get $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($get('is_external_uri') && ! empty($value)) {
                                        $appDomain = parse_url(env('APP_URL'), PHP_URL_HOST);
                                        $uriDomain = parse_url($value, PHP_URL_HOST);
                                        if ($uriDomain === $appDomain) {
                                            $fail('External URI cannot use the same domain as this application.');
                                        }
                                    }
                                };
                            },
                        ]),
                    TextInput::make('exact_match_uri')
                        ->label('Exact Match URI')
                        ->url()
                        ->columnSpanFull()
                        ->nullable(),
                ])
                ->fillForm(fn (): array => [
                    'is_external_uri' => $this->record->uri_type === UriTypes::external,
                    'uri' => $this->record->uri,
                    'exact_match_uri' => $this->record->exact_match_uri,
                ])
                ->action(function (array $data): void {
                    if ($data['is_external_uri']) {
                        $uriType = UriTypes::external;
                        $uri = $data['uri'];
                    } else {
                        $uriType = UriTypes::internal;
                        $uri = $this->record->generateInternalUri();
                    }

                    $this->record->update([
                        'uri_type' => $uriType,
                        'uri' => $uri,
                        'exact_match_uri' => $data['exact_match_uri'],
                    ]);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
