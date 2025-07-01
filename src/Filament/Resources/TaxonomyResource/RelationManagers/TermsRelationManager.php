<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Enums\UriTypes;

class TermsRelationManager extends RelationManager
{
    protected static string $relationship = 'terms';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(Term::class, 'name', ignoreRecord: true),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Term')
                    ->options(function (Forms\Get $get) {
                        $currentId = $get('id');
                        return Term::where('id', '!=', $currentId)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->nullable()
                    ->preload()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if ($value && request()->route('record')) {
                                    $currentId = request()->route('record');
                                    if ($value == $currentId) {
                                        $fail('A term cannot be its own parent.');
                                    }
                                }
                            };
                        },
                    ]),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()->preloadRecordSelect(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Action::make('semantic_data')
                        ->label('Manage Semantic Metadata')
                        ->icon('heroicon-o-code-bracket')
                        ->color('info')
                        ->form([
                            Forms\Components\Toggle::make('is_external_uri')
                                ->label('Use External URI')
                                ->helperText('Enable to define a custom external URI instead of auto-generated internal URI')
                                ->live()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('uri')
                                ->label('URI')
                                ->required()
                                ->url()
                                ->columnSpanFull()
                                ->disabled(fn (Forms\Get $get) => !$get('is_external_uri'))
                                ->helperText('Must not use the same domain as this application')
                                ->rules([
                                    function (Forms\Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                            if ($get('is_external_uri') && !empty($value)) {
                                                $appDomain = parse_url(env('APP_URL'), PHP_URL_HOST);
                                                $uriDomain = parse_url($value, PHP_URL_HOST);
                                                if ($uriDomain === $appDomain) {
                                                    $fail('External URI cannot use the same domain as this application.');
                                                }
                                            }
                                        };
                                    },
                                ]),
                            Forms\Components\TextInput::make('exact_match_uri')
                                ->label('Exact Match URI')
                                ->url()
                                ->columnSpanFull()
                                ->nullable(),
                        ])
                        ->fillForm(fn (Term $record): array => [
                            'is_external_uri' => $record->uri_type === UriTypes::external,
                            'uri' => $record->uri,
                            'exact_match_uri' => $record->exact_match_uri,
                        ])
                        ->action(function (array $data, Term $record): void {
                            if ($data['is_external_uri']) {
                                $uriType = UriTypes::external;
                                $uri = $data['uri'];
                            } else {
                                $uriType = UriTypes::internal;
                                $uri = $record->generateInternalUri();
                            }

                            $record->update([
                                'uri_type' => $uriType,
                                'uri' => $uri,
                                'exact_match_uri' => $data['exact_match_uri'],
                            ]);
                        }),
                    Tables\Actions\DetachAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->iconButton()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
