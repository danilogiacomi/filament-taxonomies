<?php

namespace Net7\FilamentTaxonomies\Filament\Resources;

use Net7\FilamentTaxonomies\Filament\Resources\ConceptResource\Pages;
use Net7\FilamentTaxonomies\Models\Concept;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Component as Livewire;
use Net7\FilamentTaxonomies\Models\ConceptSchema;
use Illuminate\Database\Eloquent\Model;

class ConceptResource extends Resource
{
    protected static ?string $model = Concept::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }
    private static function generateUniqueURI(ConceptSchema $ownerRecord): string
    {
        $uuid = Str::uuid();
        $concept = Concept::where([
            'uri' => $uuid,
            'concept_schema_id' => $ownerRecord->id,
        ])->first();

        if ($concept) {
            return self::generateUniqueURI($ownerRecord);
        }

        return $uuid;
    }
    private static function getURI(ConceptSchema $ownerRecord): string
    {
        $baseURI = config('app.url') . '/taxonomy';
        if (isset($ownerRecord) && $ownerRecord->exists) {
            $parentURI = $ownerRecord->uri ?? 'unknown_schema';
            return "{$baseURI}/{$parentURI}";
        }
        return "{$baseURI}/unknown";
    }

    private static function resolveOwnerRecord(?ConceptSchema $ownerRecordParam, Livewire $livewire, ?Model $record): ?ConceptSchema
    {
        // 1. Use ownerRecord passed directly to getFormSchema if available
        if ($ownerRecordParam instanceof ConceptSchema) {
            return $ownerRecordParam;
        }

        // 2. If editing a Concept, get owner from the relationship
        if ($record instanceof Concept && $record->conceptSchema) {
            return $record->conceptSchema;
        }

        // 3. Try getting ownerRecord from the Livewire component (Page or RelationManager)
        if (method_exists($livewire, 'getOwnerRecord') && ($owner = $livewire->getOwnerRecord()) instanceof ConceptSchema) {
            return $owner;
        }
        if (property_exists($livewire, 'ownerRecord') && $livewire->ownerRecord instanceof ConceptSchema) {
            return $livewire->ownerRecord;
        }

        return null;
    }

    public static function getFormSchema(ConceptSchema $ownerRecord = null): array
    {
        return [
            TextInput::make('label')
                ->required()
                ->columnSpanFull(),
            TextInput::make('uri')
                ->label('URI')
                ->readOnly()
                ->default(function (Livewire $livewire, ?Model $record) use ($ownerRecord) {
                    $resolvedOwner = self::resolveOwnerRecord($ownerRecord, $livewire, $record);
                    return $resolvedOwner ? self::generateUniqueURI($resolvedOwner) : null; 
                })
                ->prefix(function (Livewire $livewire, ?Model $record) use ($ownerRecord) {
                    $resolvedOwner = self::resolveOwnerRecord($ownerRecord, $livewire, $record);
                    return $resolvedOwner ? (self::getURI($resolvedOwner) . '#') : (config('app.url') . '/taxonomy/unknown_schema#');
                })
                ->columnSpanFull(),
            TextInput::make('exact_match')
                ->label('Exact Match')
                ->url()
                ->columnSpanFull(),
            TextInput::make('close_match')
                ->label('Close Match')
                ->url()
                ->columnSpanFull(),
            Textarea::make('definition')
                ->rows(4)
                ->autosize()
                ->columnSpanFull(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableColumns())
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
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('label')
                ->sortable()
                ->searchable(),
            TextColumn::make('uri')
                ->label('URI')
                ->color('primary')
                ->formatStateUsing(fn ($record, Livewire $livewire) => self::getURI($livewire->getOwnerRecord()) . '#' . $record->uri)
                ->openUrlInNewTab()
                ->url(fn ($record, Livewire $livewire) => self::getURI($livewire->getOwnerRecord()) . '#' . $record->uri)
                ->limit(50)
                ->tooltip(fn ($record, Livewire $livewire) => self::getURI($livewire->getOwnerRecord()) . '#' . $record->uri)
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('exact_match')
                ->label('Exact Match')
                ->url(fn ($record) => $record->exact_match)
                ->openUrlInNewTab()
                ->limit(length: 25)
                ->tooltip(fn ($record) => $record->exact_match)
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('close_match')
                ->label('Close Match')
                ->url(fn ($record) => $record->close_match)
                ->openUrlInNewTab()
                ->limit(length: 25)
                ->tooltip(fn ($record) => $record->close_match)
                ->wrap()
                ->sortable()
                ->searchable(),
        ];
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
            'index' => Pages\ListConcepts::route('/'),
            'create' => Pages\CreateConcept::route('/create'),
            'edit' => Pages\EditConcept::route('/{record}/edit'),
        ];
    }
}
