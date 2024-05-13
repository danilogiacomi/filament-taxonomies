<?php

namespace Net7\FilamentTaxonomies\Filament\Pages;

use Net7\FilamentTaxonomies\Models\Concept as TreePageModel;
use Filament\Forms;
use Filament\Pages\Actions\CreateAction;

use Illuminate\Database\Eloquent\Builder;
use Net7\FilamentTaxonomies\Filament\Resources\ConceptResource;
use SolutionForest\FilamentTree\Actions;
use SolutionForest\FilamentTree\Concern;
use SolutionForest\FilamentTree\Pages\TreePage as BasePage;
use SolutionForest\FilamentTree\Support\Utils;

class Concept extends BasePage
{
    protected static string $model = TreePageModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static int $maxDepth = 999;

    protected function getActions(): array
    {
        return [
            $this->getCreateAction(),
            // SAMPLE CODE, CAN DELETE
            //\Filament\Pages\Actions\Action::make('sampleAction'),
        ];
    }


    protected function getTreeQuery(): Builder
    {
        return $this->getModel()::query()->where('concept_schema_id', 1);

        // return $this->traitGetTreeQuery();
    }

    
    protected function getFormSchema(): array
    {
        return ConceptResource::getFormSchema();
    }

    protected function hasDeleteAction(): bool
    {
        return false;
    }

    protected function hasEditAction(): bool
    {
        return true;
    }

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    // CUSTOMIZE ICON OF EACH RECORD, CAN DELETE
    // public function getTreeRecordIcon(?\Illuminate\Database\Eloquent\Model $record = null): ?string
    // {
    //     return null;
    // }
}