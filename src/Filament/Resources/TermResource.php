<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Filament\Tables\Actions\ActionGroup;
use Net7\FilamentTaxonomies\Filament\Resources\TermResource\Pages;
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Enums\UriTypes;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

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
                TextColumn::make('taxonomies_count')->counts('taxonomies'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Action::make('semantic_data')
                    ->label('Manage Semantic Metadata')
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
                        TextInput::make('exact_match_uri')
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
