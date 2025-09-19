<?php

namespace Net7\FilamentTaxonomies\Tests\Models;

use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;
use Net7\FilamentTaxonomies\Enums\UriTypes;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Tests\TestCase;

class TermTest extends TestCase
{
    /** @test */
    public function it_can_create_a_term()
    {
        $term = Term::create([
            'name' => 'Web Development',
            'description' => 'All about web development',
        ]);

        $this->assertDatabaseHas('terms', [
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'All about web development',
        ]);
    }

    /** @test */
    public function it_auto_generates_slug_from_name()
    {
        $term = Term::create([
            'name' => 'React & Vue.js Development',
        ]);

        $this->assertEquals('react-vuejs-development', $term->slug);
    }

    /** @test */
    public function it_can_have_parent_child_relationships()
    {
        $parent = Term::create(['name' => 'Technology']);
        $child = Term::create([
            'name' => 'Web Development',
            'parent_id' => $parent->id,
        ]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertTrue($parent->children->contains($child));
    }

    /** @test */
    public function it_calculates_hierarchy_level_correctly()
    {
        $level0 = Term::create(['name' => 'Root']);
        $level1 = Term::create(['name' => 'Level 1', 'parent_id' => $level0->id]);
        $level2 = Term::create(['name' => 'Level 2', 'parent_id' => $level1->id]);
        $level3 = Term::create(['name' => 'Level 3', 'parent_id' => $level2->id]);

        $this->assertEquals(0, $level0->calculateLevel());
        $this->assertEquals(1, $level1->calculateLevel());
        $this->assertEquals(2, $level2->calculateLevel());
        $this->assertEquals(3, $level3->calculateLevel());
    }

    /** @test */
    public function it_validates_hierarchy_level_limit()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Term hierarchy cannot exceed 10 levels');

        // Create a chain of 11 levels (0-10)
        $current = Term::create(['name' => 'Level 0']);

        for ($i = 1; $i <= Term::MAX_HIERARCHY_LEVEL; $i++) {
            $current = Term::create([
                'name' => "Level $i",
                'parent_id' => $current->id,
            ]);
        }

        // This should fail (level 11)
        Term::create([
            'name' => 'Level 11',
            'parent_id' => $current->id,
        ]);
    }

    /** @test */
    public function it_belongs_to_many_taxonomies()
    {
        $term = Term::create(['name' => 'Web Development']);

        $taxonomy1 = Taxonomy::create([
            'name' => 'Skills',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $taxonomy2 = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term->taxonomies()->attach([$taxonomy1->id, $taxonomy2->id]);

        $this->assertCount(2, $term->taxonomies);
        $this->assertTrue($term->taxonomies->contains($taxonomy1));
        $this->assertTrue($term->taxonomies->contains($taxonomy2));
    }

    /** @test */
    public function it_generates_internal_uri_correctly()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Web Development']);
        $term->taxonomies()->attach($taxonomy->id);

        $expectedUri = env('APP_URL', 'http://localhost').'/taxonomies/categories/web-development';
        $this->assertEquals($expectedUri, $term->generateInternalUri());
    }

    /** @test */
    public function it_sets_uri_type_to_internal_by_default()
    {
        $term = Term::create(['name' => 'Test Term']);

        $this->assertEquals(UriTypes::internal, $term->uri_type);
        $this->assertNotEmpty($term->uri);
    }

    /** @test */
    public function it_validates_external_uri_domain()
    {
        $term = Term::create([
            'name' => 'Test Term',
            'uri_type' => UriTypes::external,
            'uri' => 'https://external-domain.com/test',
        ]);

        $this->assertTrue($term->validateExternalUri());

        $term->uri = env('APP_URL', 'http://localhost').'/test';
        $this->assertFalse($term->validateExternalUri());
    }

    /** @test */
    public function it_can_find_term_by_taxonomy_id_and_name_or_slug_or_alias()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'aliases' => ['Web Dev', 'Web Development', 'Web'],
        ]);
        $term->taxonomies()->attach($taxonomy->id);

        $foundTerm = Term::findByTaxonomyIdAndNameOrSlugOrAlias($taxonomy->id, 'Web Development');
        $this->assertEquals($term->id, $foundTerm->id);

        $foundTerm = Term::findByTaxonomyIdAndNameOrSlugOrAlias($taxonomy->id, 'Web Dev');
        $this->assertEquals($term->id, $foundTerm->id);

        $foundTerm = Term::findByTaxonomyIdAndNameOrSlugOrAlias($taxonomy->id, 'web-development');
        $this->assertEquals($term->id, $foundTerm->id);

        $foundTerm = Term::findByTaxonomyIdAndNameOrSlugOrAlias($taxonomy->id, 'Web');
        $this->assertEquals($term->id, $foundTerm->id);

        $foundTerm = Term::findByTaxonomyIdAndNameOrSlugOrAlias($taxonomy->id, 'development');
        $this->assertNull($foundTerm);

        $foundTerm = Term::findByTaxonomyIdAndNameOrSlugOrAlias(-20, 'Web');
        $this->assertEquals($term->id, $foundTerm->id);
    }

    /** @test */
    public function it_can_find_term_by_taxonomy_id_and_and_parent_id_and_name_or_slug_or_alias()
    {
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);
        $taxonomy->refresh();

        $parent = Term::create([
            'name' => 'Parent',
            'slug' => 'parent',
        ]);
        $parent->taxonomies()->attach($taxonomy->id);
        $parent->refresh();

        $child = Term::create([
            'name' => 'Child',
            'slug' => 'child',
            'parent_id' => $parent->id,
        ]);
        $child->taxonomies()->attach($taxonomy->id);
        $child->refresh();


        $this->assertEquals('Child', $child->name);
        $this->assertEquals('child', $child->slug);
        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertEquals($taxonomy->id, $child->taxonomies->first()->id);


        $foundTerm = Term::findByTaxonomyIdAndParentIdAndNameOrSlugOrAlias($taxonomy->id, $parent->id, 'Child');
        $this->assertNotNull($foundTerm);
        $this->assertEquals($child->id, $foundTerm->id);

        $foundTerm = Term::findByTaxonomyIdAndParentIdAndNameOrSlugOrAlias($taxonomy->id, -20, 'Child');
        $this->assertNull($foundTerm);
    }
}
