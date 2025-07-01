<?php

namespace Net7\FilamentTaxonomies\Traits;

use Net7\FilamentTaxonomies\Models\EntityTerm;

trait HasTaxonomies
{
    public function entityTerms()
    {
        return $this->morphMany(EntityTerm::class, 'entity');
    }

    public function getTermsForTaxonomy(string $taxonomyType): array
    {
        return $this->entityTerms()
            ->where('taxonomy_type', $taxonomyType)
            ->with('term')
            ->get()
            ->pluck('term')
            ->toArray();
    }

    public function setTermsForTaxonomy(string $taxonomyType, array $termIds): void
    {
        // Remove existing terms for this taxonomy
        $this->entityTerms()
            ->where('taxonomy_type', $taxonomyType)
            ->delete();

        // Add new terms
        foreach ($termIds as $termId) {
            $this->entityTerms()->create([
                'taxonomy_type' => $taxonomyType,
                'term_id' => $termId
            ]);
        }
    }

    public function hasTermInTaxonomy(string $taxonomyType, int $termId): bool
    {
        return $this->entityTerms()
            ->where('taxonomy_type', $taxonomyType)
            ->where('term_id', $termId)
            ->exists();
    }
}