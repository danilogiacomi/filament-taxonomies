<?php

namespace Net7\FilamentTaxonomies\Tests\Models;

use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;
use Net7\FilamentTaxonomies\Tests\TestCase;

class TaxonomyTest extends TestCase
{
    /** @test */
    public function it_can_create_a_taxonomy()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Product Categories',
            'description' => 'Categories for products',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $this->assertDatabaseHas('taxonomies', [
            'name' => 'Product Categories',
            'slug' => 'product-categories',
            'description' => 'Categories for products',
        ]);
    }

    /** @test */
    public function it_auto_generates_slug_from_name()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Product Categories & Tags',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $this->assertEquals('product-categories-tags', $taxonomy->slug);
    }

    /** @test */
    public function it_updates_slug_when_name_changes()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Original Name',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $taxonomy->update(['name' => 'Updated Name']);

        $this->assertEquals('updated-name', $taxonomy->fresh()->slug);
    }

    /** @test */
    public function it_can_have_many_terms()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term1 = Term::create(['name' => 'Term 1']);
        $term2 = Term::create(['name' => 'Term 2']);

        $taxonomy->terms()->attach([$term1->id, $term2->id]);

        $this->assertCount(2, $taxonomy->terms);
        $this->assertTrue($taxonomy->terms->contains($term1));
        $this->assertTrue($taxonomy->terms->contains($term2));
    }

    /** @test */
    public function it_generates_internal_uri()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Product Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $expectedUri = env('APP_URL', 'http://localhost') . '/product-categories';
        $this->assertEquals($expectedUri, $taxonomy->uri);
    }

    /** @test */
    public function slug_is_unique()
    {
        Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Taxonomy::create([
            'name' => 'Categories', // Same name, should generate same slug
            'state' => TaxonomyStates::working,
            'type' => TaxonomyTypes::private,
        ]);
    }

    /** @test */
    public function it_updates_uri_when_name_changes()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Original Name',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $originalUri = $taxonomy->uri;
        $this->assertEquals('http://localhost/original-name', $originalUri);

        $taxonomy->update(['name' => 'Updated Name & Categories']);

        $this->assertEquals('http://localhost/updated-name-categories', $taxonomy->fresh()->uri);
        $this->assertNotEquals($originalUri, $taxonomy->fresh()->uri);
    }

    /** @test */
    public function it_preserves_manual_uri_when_not_updating_name()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Test Taxonomy',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $taxonomy->update(['uri' => 'http://localhost/custom-uri']);
        $taxonomy->update(['description' => 'Updated description']);

        $this->assertEquals('http://localhost/custom-uri', $taxonomy->fresh()->uri);
    }
}