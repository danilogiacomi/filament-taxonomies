<?php

namespace Net7\FilamentTaxonomies\Forms\Components;

use Filament\Forms\Components\Select;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\EntityTerm;

class TaxonomySelect extends Select
{
    protected string $taxonomy;

    public function taxonomy(string $taxonomy): static
    {
        $this->taxonomy = $taxonomy;

        $this->options(function () use ($taxonomy) {
            $taxonomyModel = Taxonomy::where('name', $taxonomy)->first();
            if (!$taxonomyModel) {
                return [];
            }

            return $taxonomyModel->terms->pluck('name', 'id')->toArray();
        });

        $this->dehydrated(false);

        $this->afterStateHydrated(function (Select $component, $state, $record) use ($taxonomy) {
            if ($record) {
                $existingTerm = $record->entityTerms()
                    ->where('taxonomy_type', $taxonomy)
                    ->first();

                if ($existingTerm) {
                    $component->state($existingTerm->term_id);
                }
            }
        });

        $this->saveRelationshipsUsing(function (Select $component, $state, $record) use ($taxonomy) {
            if ($record) {
                $this->saveEntityTerm($record, $taxonomy, $state);
            }
        });

        return $this;
    }

    protected function saveEntityTerm($record, string $taxonomyType, $termId): void
    {
        if ($termId) {
            EntityTerm::updateOrCreate(
                [
                    'entity_type' => get_class($record),
                    'entity_id' => $record->id,
                    'taxonomy_type' => $taxonomyType
                ],
                [
                    'term_id' => $termId
                ]
            );
        } else {
            // Remove if no term selected
            EntityTerm::where('entity_type', get_class($record))
                ->where('entity_id', $record->id)
                ->where('taxonomy_type', $taxonomyType)
                ->delete();
        }
    }
}