<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Filament\Tables\Actions\ActionGroup;
use Net7\FilamentTaxonomies\Filament\Resources\TaxonomyResource\Pages;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;

class TaxonomyResource extends Resource
{
    protected static ?string $model = Taxonomy::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')
                        ->required()
                        ->unique(Taxonomy::class, 'name', ignoreRecord: true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            if ($state) {
                                $set('slug', Str::slug($state));
                            }
                        }),
                    TextInput::make('slug')
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Auto-generated from name'),
                    Textarea::make('description')
                ]),
                Section::make([
                    Select::make('state')
                        ->options(TaxonomyStates::options())
                        ->required(),
                    Select::make('type')
                        ->options(TaxonomyTypes::options())
                        ->required()
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')->limit(50),
                TextColumn::make('state')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof TaxonomyStates ? $state->getLabel() : $state)
                    ->icon(fn ($state) => $state instanceof TaxonomyStates ? $state->getIcon() : null)
                    ->color(fn ($state) => $state instanceof TaxonomyStates ? $state->getColor() : 'gray'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof TaxonomyTypes ? $state->getLabel() : $state)
                    ->icon(fn ($state) => $state instanceof TaxonomyTypes ? $state->getIcon() : null)
                    ->color(fn ($state) => $state instanceof TaxonomyTypes ? $state->getColor() : 'gray'),
                TextColumn::make('terms_count')->counts('terms'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ])
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
             TaxonomyResource\RelationManagers\TermsRelationManager::class,
         ];
     }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxonomies::route('/'),
            'create' => Pages\CreateTaxonomy::route('/create'),
            'edit' => Pages\EditTaxonomy::route('/{record}/edit'),
        ];
    }
}
