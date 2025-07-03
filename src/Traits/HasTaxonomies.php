<?php

namespace Net7\FilamentTaxonomies\Traits;

use Net7\FilamentTaxonomies\Models\EntityTerm;
use Net7\FilamentTaxonomies\Models\Taxonomy;

trait HasTaxonomies
{
    public function entityTerms()
    {
        return $this->morphMany(EntityTerm::class, 'entity');
    }

    /**
     * Get terms for taxonomy by ID (recommended)
     */
    public function getTermsForTaxonomyId(int $taxonomyId)
    {
        return $this->entityTerms()
            ->where('taxonomy_id', $taxonomyId)
            ->with('term')
            ->get()
            ->pluck('term');
    }

    /**
     * Set terms for taxonomy by ID (recommended)
     */
    public function setTermsForTaxonomyId(int $taxonomyId, array $termIds): void
    {
        // Remove existing terms for this taxonomy
        $this->entityTerms()
            ->where('taxonomy_id', $taxonomyId)
            ->delete();

        // Add new terms
        foreach ($termIds as $termId) {
            $this->entityTerms()->create([
                'taxonomy_id' => $taxonomyId,
                'term_id' => $termId
            ]);
        }
    }

    /**
     * Check if entity has term in taxonomy by ID (recommended)
     */
    public function hasTermInTaxonomyId(int $taxonomyId, int $termId): bool
    {
        return $this->entityTerms()
            ->where('taxonomy_id', $taxonomyId)
            ->where('term_id', $termId)
            ->exists();
    }

    /**
     * Get terms for taxonomy by slug (recommended)
     */
    public function getTermsForTaxonomySlug(string $taxonomySlug)
    {
        $taxonomy = Taxonomy::where('slug', $taxonomySlug)->first();
        if (!$taxonomy) {
            return collect();
        }

        return $this->getTermsForTaxonomyId($taxonomy->id);
    }

    /**
     * Set terms for taxonomy by slug (recommended)
     */
    public function setTermsForTaxonomySlug(string $taxonomySlug, array $termIds): void
    {
        $taxonomy = Taxonomy::where('slug', $taxonomySlug)->first();
        if (!$taxonomy) {
            return;
        }

        $this->setTermsForTaxonomyId($taxonomy->id, $termIds);
    }

    /**
     * Check if entity has term in taxonomy by slug (recommended)
     */
    public function hasTermInTaxonomySlug(string $taxonomySlug, int $termId): bool
    {
        $taxonomy = Taxonomy::where('slug', $taxonomySlug)->first();
        if (!$taxonomy) {
            return false;
        }

        return $this->hasTermInTaxonomyId($taxonomy->id, $termId);
    }

    /**
     * Get terms for taxonomy by name (legacy support)
     * @deprecated Use getTermsForTaxonomySlug() instead
     */
    public function getTermsForTaxonomy(string $taxonomyName)
    {
        $taxonomy = Taxonomy::where('name', $taxonomyName)->first();
        if (!$taxonomy) {
            return collect();
        }

        return $this->getTermsForTaxonomyId($taxonomy->id);
    }

    /**
     * Set terms for taxonomy by name (legacy support)
     * @deprecated Use setTermsForTaxonomySlug() instead
     */
    public function setTermsForTaxonomy(string $taxonomyName, array $termIds): void
    {
        $taxonomy = Taxonomy::where('name', $taxonomyName)->first();
        if (!$taxonomy) {
            return;
        }

        $this->setTermsForTaxonomyId($taxonomy->id, $termIds);
    }

    /**
     * Check if entity has term in taxonomy by name (legacy support)
     * @deprecated Use hasTermInTaxonomySlug() instead
     */
    public function hasTermInTaxonomy(string $taxonomyName, int $termId): bool
    {
        $taxonomy = Taxonomy::where('name', $taxonomyName)->first();
        if (!$taxonomy) {
            return false;
        }

        return $this->hasTermInTaxonomyId($taxonomy->id, $termId);
    }
}
