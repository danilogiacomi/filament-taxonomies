<?php

namespace Net7\FilamentTaxonomies\Forms\Components;

use Closure;
use Filament\Forms\Components\Select;
use Net7\FilamentTaxonomies\Models\EntityTerm;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\Term;

class TaxonomySelect extends Select
{
    protected string $taxonomy;

    protected Closure|bool $isMultiple = false;

    protected ?int $maxLevel = null;

    protected ?int $minLevel = null;

    protected ?int $exactLevel = null;

    protected ?string $parentItemFrom = null;

    protected ?string $type = null;

    public function multiple(Closure|bool $condition = true): static
    {
        $this->isMultiple = true;
        parent::multiple($condition);

        return $this;
    }

    public function maxLevel(int $level): static
    {
        if ($level < 0 || $level > Term::MAX_HIERARCHY_LEVEL) {
            throw new \InvalidArgumentException(
                'Level must be between 0 and '.Term::MAX_HIERARCHY_LEVEL
            );
        }

        $this->maxLevel = $level;

        return $this;
    }

    public function minLevel(int $level): static
    {
        if ($level < 0 || $level > Term::MAX_HIERARCHY_LEVEL) {
            throw new \InvalidArgumentException(
                'Level must be between 0 and '.Term::MAX_HIERARCHY_LEVEL
            );
        }

        $this->minLevel = $level;

        return $this;
    }

    public function exactLevel(int $level): static
    {
        if ($level < 0 || $level > Term::MAX_HIERARCHY_LEVEL) {
            throw new \InvalidArgumentException(
                'Level must be between 0 and '.Term::MAX_HIERARCHY_LEVEL
            );
        }

        $this->exactLevel = $level;

        return $this;
    }

    public function rootLevel(): static
    {
        return $this->exactLevel(0);
    }

    public function getExactLevel(): ?int
    {
        return $this->exactLevel;
    }

    public function getMinLevel(): ?int
    {
        return $this->minLevel;
    }

    public function getMaxLevel(): ?int
    {
        return $this->maxLevel;
    }

    public function getTaxonomy(): ?string
    {
        return $this->taxonomy;
    }

    public function getType(): ?string
    {
        if ($this->type) {
            return $this->type;
        }

        return $this->getName();
    }

    public function isMultiple(): bool
    {
        return $this->evaluate($this->isMultiple);
    }

    public function parentItemFrom(string $parentItemFrom): static
    {
        $this->parentItemFrom = $parentItemFrom;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function taxonomy(string $taxonomySlug): static
    {
        $this->taxonomy = $taxonomySlug;

        $this->options(function ($get) use ($taxonomySlug) {
            $taxonomyModel = Taxonomy::where('slug', $taxonomySlug)->first();
            if (! $taxonomyModel) {
                return [];
            }

            if ($this->parentItemFrom) {
                $parentItemId = $get($this->parentItemFrom);
                if (! $parentItemId) {
                    return [];
                }

                $terms = $taxonomyModel->terms->where('parent_id', $parentItemId);
            } else {
                $terms = $taxonomyModel->terms;
            }

            if ($this->exactLevel !== null || $this->minLevel !== null || $this->maxLevel !== null) {
                $terms = $terms->filter(function ($term) {
                    $level = $term->calculateLevel();

                    if ($this->exactLevel !== null) {
                        return $level === $this->exactLevel;
                    }

                    if ($this->minLevel !== null && $level < $this->minLevel) {
                        return false;
                    }

                    if ($this->maxLevel !== null && $level > $this->maxLevel) {
                        return false;
                    }

                    return true;
                });
            }

            return $terms->sortBy('name')->pluck('name', 'id')->toArray();
        });

        $this->dehydrated(false);

        $this->afterStateHydrated(function (Select $component, $state, $record) use ($taxonomySlug) {
            if ($record) {
                $taxonomyModel = Taxonomy::where('slug', $taxonomySlug)->first();
                if (! $taxonomyModel) {
                    return;
                }

                if ($this->evaluate($this->isMultiple)) {
                    $existingTerms = $record->getTermsByType($this->getType())
                        ->where('taxonomy_id', $taxonomyModel->id)
                        ->pluck('term_id')
                        ->toArray();

                    $component->state($existingTerms);
                } else {
                    $existingTerm = $record->getTermsByType($this->getType())
                        ->where('taxonomy_id', $taxonomyModel->id)
                        ->first();

                    if ($existingTerm) {
                        $component->state($existingTerm->term_id);
                    }
                }
            }
        });

        $this->saveRelationshipsUsing(function (Select $component, $state, $record) use ($taxonomySlug) {
            if ($record) {
                $taxonomyModel = Taxonomy::where('slug', $taxonomySlug)->first();
                if (! $taxonomyModel) {
                    return;
                }

                if ($this->evaluate($this->isMultiple)) {
                    $this->saveMultipleEntityTerms($record, $taxonomyModel->id, $state);
                } else {
                    $this->saveEntityTerm($record, $taxonomyModel->id, $state);
                }
            }
        });

        return $this;
    }

    protected function saveMultipleEntityTerms($record, int $taxonomyId, array $termIds): void
    {
        EntityTerm::where('entity_type', get_class($record))
            ->where('entity_id', $record->id)
            ->where('taxonomy_id', $taxonomyId)
            ->delete();

        if (! empty($termIds)) {
            $data = [];
            foreach ($termIds as $termId) {
                $data[] = [
                    'entity_type' => get_class($record),
                    'entity_id' => $record->id,
                    'taxonomy_id' => $taxonomyId,
                    'term_id' => $termId,
                    'type' => $this->getType(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            EntityTerm::insert($data);
        }
    }

    protected function saveEntityTerm($record, int $taxonomyId, $termId): void
    {
        if ($termId) {
            EntityTerm::updateOrCreate(
                [
                    'entity_type' => get_class($record),
                    'entity_id' => $record->id,
                    'taxonomy_id' => $taxonomyId,
                    'type' => $this->getType(),
                ],
                [
                    'term_id' => $termId,
                ]
            );
        } else {
            EntityTerm::where('entity_type', get_class($record))
                ->where('entity_id', $record->id)
                ->where('taxonomy_id', $taxonomyId)
                ->delete();
        }
    }
}
