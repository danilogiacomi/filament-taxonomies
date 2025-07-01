<?php

namespace Net7\FilamentTaxonomies\Forms\Components;

use Filament\Forms\Components\Select;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\EntityTerm;
use Closure;

class TaxonomySelect extends Select
{
    protected string $taxonomy;
    protected Closure|bool $isMultiple = false;

    public function multiple(Closure|bool $condition = true): static
    {
        $this->isMultiple = true;
        parent::multiple($condition);
        return $this;
    }

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
                if ($this->evaluate($this->isMultiple)) {
                    $existingTerms = $record->entityTerms()
                        ->where('taxonomy_type', $taxonomy)
                        ->pluck('term_id')
                        ->toArray();

                    $component->state($existingTerms);
                } else {
                    $existingTerm = $record->entityTerms()
                        ->where('taxonomy_type', $taxonomy)
                        ->first();

                    if ($existingTerm) {
                        $component->state($existingTerm->term_id);
                    }
                }
            }
        });

        $this->saveRelationshipsUsing(function (Select $component, $state, $record) use ($taxonomy) {
            if ($record) {
                if ($this->evaluate($this->isMultiple)) {
                    $this->saveMultipleEntityTerms($record, $taxonomy, $state);
                } else {
                    $this->saveEntityTerm($record, $taxonomy, $state);
                }
            }
        });

        return $this;
    }

    protected function saveMultipleEntityTerms($record, string $taxonomyType, array $termIds): void
    {
        EntityTerm::where('entity_type', get_class($record))
            ->where('entity_id', $record->id)
            ->where('taxonomy_type', $taxonomyType)
            ->delete();

        if (!empty($termIds)) {
            $data = [];
            foreach ($termIds as $termId) {
                $data[] = [
                    'entity_type' => get_class($record),
                    'entity_id' => $record->id,
                    'taxonomy_type' => $taxonomyType,
                    'term_id' => $termId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            EntityTerm::insert($data);
        }
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
            EntityTerm::where('entity_type', get_class($record))
                ->where('entity_id', $record->id)
                ->where('taxonomy_type', $taxonomyType)
                ->delete();
        }
    }
}
