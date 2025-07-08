<?php

namespace Net7\FilamentTaxonomies\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Net7\FilamentTaxonomies\Models\EntityTerm;
use Net7\FilamentTaxonomies\Models\Taxonomy;

trait HasTaxonomies
{
    public static function bootHasTaxonomies()
    {
        static::deleting(function ($model) {
            $model->entityTerms()->delete();
        });
    }

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
     * Get terms for relation type (recommended)
     */
    public function getTermsForRelationType(string $relationType)
    {
        return $this->entityTerms()
            ->where('type', $relationType)
            ->with('term')
            ->get()
            ->pluck('term');
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
     * Set terms for taxonomy by ID (recommended)
     */
    public function setTermsForTaxonomyId(int $taxonomyId, array $termIds, string|null $relationType = null): void
    {
        if ($relationType === null) {
            $taxonomy = Taxonomy::find($taxonomyId);
            if (! $taxonomy) {
                return;
            }

            $relationType = $taxonomy->slug;
        }

        // Remove existing terms for this taxonomy
        $this->entityTerms()
            ->where('taxonomy_id', $taxonomyId)
            ->delete();

        // Add new terms
        foreach ($termIds as $termId) {
            $this->entityTerms()->create([
                'taxonomy_id' => $taxonomyId,
                'term_id' => $termId,
                'type' => $relationType,
            ]);
        }
    }



    public function hasTermInRelationType(string $relationType, int $termId): bool
    {
        return $this->entityTerms()
            ->where('type', $relationType)
            ->where('term_id', $termId)
            ->exists();
    }

    /**
     * Get terms for taxonomy by slug (recommended)
     */
    public function getTermsForTaxonomySlug(string $taxonomySlug)
    {
        $taxonomy = Taxonomy::where('slug', $taxonomySlug)->first();
        if (! $taxonomy) {
            return collect();
        }

        return $this->getTermsForTaxonomyId($taxonomy->id);
    }

    /**
     * Set terms for taxonomy by slug (recommended)
     */
    public function setTermsForTaxonomySlug(string $taxonomySlug, array $termIds, string|null $relationType = null): void
    {
        $taxonomy = Taxonomy::where('slug', $taxonomySlug)->first();
        if (! $taxonomy) {
            return;
        }

        if ($relationType === null) {
            $relationType = $taxonomy->slug;
        }

        $this->setTermsForTaxonomyId($taxonomy->id, $termIds, $relationType);
    }

    /**
     * Check if entity has term in taxonomy by slug (recommended)
     */
    public function hasTermInTaxonomySlug(string $taxonomySlug, int $termId): bool
    {
        $taxonomy = Taxonomy::where('slug', $taxonomySlug)->first();
        if (! $taxonomy) {
            return false;
        }

        return $this->hasTermInTaxonomyId($taxonomy->id, $termId);
    }

    /**
     * Get terms for taxonomy by name (legacy support)
     *
     * @deprecated Use getTermsForTaxonomySlug() instead
     */
    public function getTermsForTaxonomy(string $taxonomyName)
    {
        $taxonomy = Taxonomy::where('name', $taxonomyName)->first();
        if (! $taxonomy) {
            return collect();
        }

        return $this->getTermsForTaxonomyId($taxonomy->id);
    }

    /**
     * Set terms for taxonomy by name (legacy support)
     *
     * @deprecated Use setTermsForTaxonomySlug() instead
     */
    public function setTermsForTaxonomy(string $taxonomyName, array $termIds): void
    {
        $taxonomy = Taxonomy::where('name', $taxonomyName)->first();
        if (! $taxonomy) {
            return;
        }

        $this->setTermsForTaxonomyId($taxonomy->id, $termIds);
    }

    /**
     * Check if entity has term in taxonomy by name (legacy support)
     *
     * @deprecated Use hasTermInTaxonomySlug() instead
     */
    public function hasTermInTaxonomy(string $taxonomyName, int $termId): bool
    {
        $taxonomy = Taxonomy::where('name', $taxonomyName)->first();
        if (! $taxonomy) {
            return false;
        }

        return $this->hasTermInTaxonomyId($taxonomy->id, $termId);
    }

    /**
     * To be implemented in the model using this trait
     *
     * This is optional, filament doesn't need this per-se, but if you want to get access
     * to terms from the model, using a relation method, you can populate this array
     * with the method name and the taxonomy_term type.
     * It will do the magic.
     *
     *
     * It's an array of methodName => taxonomy_term type
     * Example:
     *
     * return [
     *     'complexity' => 'complessità',
     *     'specific_complexity' => 'complessità-specifica',
     * ];
     *
     * And it will return the terms for the taxonomy_term type
     *
     *
     * This way you can do things like this:
     *
     * $modelInstance->complexity
     * $modelInstance->specific_complexity
     *
     * and get a collection of terms, or you can do:
     *
     * $modelInstance->complexity()
     * $modelInstance->specific_complexity()
     *
     * and get the Relations
     *
     * @return array
     */
    public function termsTypeMapping()
    {
        return [];
    }

    /**
     * Get the terms resolver for the model
     *
     * @param  string  $method
     * @return array
     */
    private function getTermsResolver($method)
    {
        $map = $this->termsTypeMapping();

        return isset($map[$method])
            ? $this->entityTerms()->where('type', $map[$method])
            : null;
    }

    public function getTermsByType($type = null)
    {
        if ($type === null) {
            return $this->entityTerms();
        }

        if (method_exists($this, $type)) {
            return $this->{$type}();
        }

        return $this->entityTerms()->where('type', $type);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $relation = $this->getTermsResolver($method);

        if ($relation instanceof Relation) {
            return $relation;
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Handle dynamic properties on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $relation = $this->getTermsResolver($key);

        if ($relation instanceof Relation) {
            return $relation->getResults();
        }

        return parent::__get($key);
    }

    public function getAttribute($key)
    {
        $relation = $this->getTermsResolver($key);

        if ($relation instanceof Relation) {
            return $this->getTermsForTaxonomySlug($relation);
        }

        return parent::getAttribute($key);
    }
}
