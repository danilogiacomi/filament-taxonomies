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
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                        if ($state) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }
                    })
                    ->rules([
                        function (Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $termId = $get('id');
                                $existingTerm = Term::where('name', $value)
                                    ->when($termId, fn($query) => $query->where('id', '!=', $termId))
                                    ->whereHas('taxonomies', function ($query) use ($get) {
                                        $taxonomyIds = collect($get('taxonomies') ?? [])->pluck('id')->filter();
                                        if ($taxonomyIds->isNotEmpty()) {
                                            $query->whereIn('taxonomies.id', $taxonomyIds);
                                        }
                                    })->first();
                                if ($existingTerm) {
                                    $fail('The name has already been taken inside the current taxonomy.');
                                }
                            };
                        },
                    ]),
                Forms\Components\TextInput::make('slug')
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Auto-generated from name'),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Term')
                    ->options(function (Forms\Get $get) {
                        $currentId = $get('id');

                        // Get all potential parent terms
                        $terms = Term::where('id', '!=', $currentId)->get();

                        // Filter out terms that would create hierarchy level > MAX_HIERARCHY_LEVEL
                        $validParents = $terms->filter(function ($term) {
                            $parentLevel = $term->calculateLevel();
                            return ($parentLevel + 1) < Term::MAX_HIERARCHY_LEVEL;
                        });

                        return $validParents->pluck('name', 'id');
                    })
                    ->searchable()
                    ->nullable()
                    ->preload()
                    ->helperText('Maximum hierarchy depth is ' . Term::MAX_HIERARCHY_LEVEL . ' levels')
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if ($value && request()->route('record')) {
                                    $currentId = request()->route('record');
                                    if ($value == $currentId) {
                                        $fail('A term cannot be its own parent.');
                                    }
                                }

                                // Additional validation for hierarchy level
                                if ($value) {
                                    $parentTerm = Term::find($value);
                                    if ($parentTerm && ($parentTerm->calculateLevel() + 1) >= Term::MAX_HIERARCHY_LEVEL) {
                                        $fail('Selecting this parent would exceed the maximum hierarchy depth of ' . Term::MAX_HIERARCHY_LEVEL . ' levels.');
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
                Tables\Columns\TextColumn::make('name')->wrap(),
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
                Tables\Actions\CreateAction::make()
                    ->after(function (Term $record) {
                        // Regenerate URI after taxonomy relationship is established
                        if ($record->uri_type === UriTypes::internal) {
                            $record->update(['uri' => $record->generateInternalUri()]);
                        }
                    }),
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
                    Tables\Actions\DeleteAction::make(),
                ])->iconButton()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
