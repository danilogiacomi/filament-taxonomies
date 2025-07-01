<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Net7\FilamentTaxonomies\Filament\Resources\TermResource\Pages;
use Net7\FilamentTaxonomies\Models\Term;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;


    public static function getFormSchema(){
        return [
            TextInput::make('name')->required()->unique(Term::class, 'name', ignoreRecord: true)->columnSpanFull(),
            Textarea::make('description')->columnSpanFull(),
            Select::make('parent_id')
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
            TextInput::make('uri')->required()->columnSpanFull(),
            TextInput::make('exact_match_uri')
                ->label('Exact Match URI')
                ->columnSpanFull()
                ->nullable(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')->limit(50),
                TextColumn::make('parent.name')->label('Parent'),
                TextColumn::make('uri'),
                TextColumn::make('taxonomies_count')->counts('taxonomies'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTerms::route('/'),
            'create' => Pages\CreateTerm::route('/create'),
            'edit' => Pages\EditTerm::route('/{record}/edit'),
        ];
    }
}
