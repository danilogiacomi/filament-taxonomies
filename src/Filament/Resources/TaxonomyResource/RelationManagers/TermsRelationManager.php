<?php

namespace Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Net7\FilamentTaxonomies\Models\Term;
use Illuminate\Support\Str;

class TermsRelationManager extends RelationManager
{
    protected static string $relationship = 'terms';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(Term::class, 'name', ignoreRecord: true)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                if (!empty($state)) {
                                    $baseUri = config('app.url', 'http://localhost') . '/taxonomy/terms/';
                                    $slug = Str::slug($state);
                                    $set('uri', $baseUri . $slug);
                                }
                            }),
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Term')
                            ->options(function (Forms\Get $get) {
                                $currentId = $get('id');
                                return Term::where('id', '!=', $currentId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->nullable()
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
                    ])
                    ->icon('heroicon-m-information-circle')
                    ->iconColor('primary'),
                Forms\Components\Section::make('Semantic Fields')
                    ->schema([
                        Forms\Components\TextInput::make('uri')
                            ->maxLength(255)
                            ->nullable()
                            ->helperText('Auto-generated from name, but can be customized'),
                        Forms\Components\TextInput::make('exact_match_uri')
                            ->label('Exact Match URI')
                            ->maxLength(255)
                            ->nullable(),
                    ])
                    ->icon('heroicon-m-link')
                    ->iconColor('success')
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
                // Tables\Columns\TextColumn::make('uri'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
