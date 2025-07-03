<?php

use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;

it('can create a complete taxonomy with terms', function () {
    $taxonomy = Taxonomy::create([
        'name' => 'Blog Categories',
        'description' => 'Categories for blog posts',
        'state' => TaxonomyStates::published,
        'type' => TaxonomyTypes::public,
    ]);

    $rootTerm = Term::create([
        'name' => 'Technology',
        'description' => 'All tech-related posts',
    ]);

    $childTerm = Term::create([
        'name' => 'Web Development',
        'description' => 'Web development articles',
        'parent_id' => $rootTerm->id,
    ]);

    $taxonomy->terms()->attach([$rootTerm->id, $childTerm->id]);

    expect($taxonomy->name)->toBe('Blog Categories')
        ->and($taxonomy->slug)->toBe('blog-categories')
        ->and($taxonomy->terms)->toHaveCount(2)
        ->and($rootTerm->calculateLevel())->toBe(0)
        ->and($childTerm->calculateLevel())->toBe(1)
        ->and($childTerm->parent->id)->toBe($rootTerm->id);
});
