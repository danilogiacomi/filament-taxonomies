<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Filament\Forms\Components\{Section, Textarea, TextInput, Radio, Grid};
use Filament\Forms\{Form, Get, Set};
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Net7\FilamentTaxonomies\Enums\ConceptSchemaStates;
use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\Pages;
use Net7\FilamentTaxonomies\Filament\Resources\ConceptSchemaResource\RelationManagers\ConceptsRelationManager;
use Net7\FilamentTaxonomies\Models\ConceptSchema;

class ConceptSchemaResource extends Resource
{
    protected static ?string $model = ConceptSchema::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Controlled Vocabulary';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('label')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('uri') ?? '') !== Str::slug($old)) {
                            return;
                        }
                        $set('uri', Str::slug($state));
                    })
                    ->required(),
                Section::make('Data')
                    ->schema([
                        TextInput::make('uri')
                            ->label('URI')
                            ->prefix(config('app.url') . '/taxonomy/')
                            ->required(),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('owner'),
                                TextInput::make('creator'),
                                TextInput::make('license'),
                            ]),
                        Textarea::make('description')
                            ->rows(4)
                            ->autosize()
                            ->columnSpanFull(),
                        Radio::make('state')
                            ->options(ConceptSchemaStates::class)
                            ->default(ConceptSchemaStates::DRAFT->value)
                            ->columnSpanFull()
                            ->required(),
                    ])
                    // Collapsible only on edit
                    ->collapsible(fn($get) => $get('id') !== null)
                    // Collapsed only on edit
                    ->collapsed(fn($get) => $get('id') !== null)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('state')
                    ->badge()
                    ->formatStateUsing(fn($state) => Str::upper(ConceptSchemaStates::from($state)->getLabel()))
                    ->color(fn($state) => ConceptSchemaStates::from($state)->getColor())
                    ->sortable(),
                TextColumn::make('concepts_count')
                    ->label('Concepts')
                    ->counts('concepts')
                    ->badge()
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->button(),
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->button()
                    ->url(fn($record) => route('filament-taxonomies-taxonomy', ['schema' => $record->label]))
                    ->icon('heroicon-m-link')
                    ->openUrlInNewTab()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ConceptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConceptSchemas::route('/'),
            'create' => Pages\CreateConceptSchema::route('/create'),
            'edit' => Pages\EditConceptSchema::route('/{record}/edit'),
        ];
    }
}
