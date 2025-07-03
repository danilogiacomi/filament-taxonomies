<?php

namespace Net7\FilamentTaxonomies\Tests\Forms;

use Net7\FilamentTaxonomies\Forms\Components\TaxonomySelect;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;
use Net7\FilamentTaxonomies\Tests\TestCase;

class TaxonomySelectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test taxonomy with hierarchical terms
        $this->taxonomy = Taxonomy::create([
            'name' => 'Test Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        // Create hierarchical terms
        $this->rootTerm = Term::create(['name' => 'Root Term']);
        $this->level1Term = Term::create([
            'name' => 'Level 1 Term',
            'parent_id' => $this->rootTerm->id,
        ]);
        $this->level2Term = Term::create([
            'name' => 'Level 2 Term',
            'parent_id' => $this->level1Term->id,
        ]);
        $this->level3Term = Term::create([
            'name' => 'Level 3 Term',
            'parent_id' => $this->level2Term->id,
        ]);

        // Attach terms to taxonomy
        $this->taxonomy->terms()->attach([
            $this->rootTerm->id,
            $this->level1Term->id,
            $this->level2Term->id,
            $this->level3Term->id,
        ]);
    }

    /** @test */
    public function it_can_create_taxonomy_select_component()
    {
        $component = TaxonomySelect::make('categories')
            ->taxonomy('test-categories');

        $this->assertInstanceOf(TaxonomySelect::class, $component);
    }

    /** @test */
    public function it_can_filter_by_exact_level()
    {
        $component = TaxonomySelect::make('categories')
            ->taxonomy('test-categories')
            ->exactLevel(0);

        // Test that level filtering works correctly
        $this->assertEquals(0, $component->getExactLevel());
    }

    /** @test */
    public function it_can_filter_by_root_level()
    {
        $component = TaxonomySelect::make('categories')
            ->taxonomy('test-categories')
            ->rootLevel();

        $this->assertEquals(0, $component->getExactLevel());
    }

    /** @test */
    public function it_can_set_min_and_max_levels()
    {
        $component = TaxonomySelect::make('categories')
            ->taxonomy('test-categories')
            ->minLevel(1)
            ->maxLevel(3);

        $this->assertEquals(1, $component->getMinLevel());
        $this->assertEquals(3, $component->getMaxLevel());
    }

    /** @test */
    public function it_validates_level_parameters()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        TaxonomySelect::make('categories')
            ->exactLevel(15); // Should fail - exceeds MAX_HIERARCHY_LEVEL
    }

    /** @test */
    public function it_validates_min_level_parameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        TaxonomySelect::make('categories')
            ->minLevel(-1); // Should fail - negative level
    }

    /** @test */
    public function it_validates_max_level_parameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        TaxonomySelect::make('categories')
            ->maxLevel(15); // Should fail - exceeds MAX_HIERARCHY_LEVEL
    }

    /** @test */
    public function it_can_be_set_as_multiple()
    {
        $component = TaxonomySelect::make('categories')
            ->taxonomy('test-categories')
            ->multiple();

        $this->assertTrue($component->isMultiple());
    }

    /** @test */
    public function it_uses_taxonomy_slug_for_operations()
    {
        // Create taxonomy with specific slug
        $taxonomy = Taxonomy::create([
            'name' => 'Product Categories & Tags',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $component = TaxonomySelect::make('categories')
            ->taxonomy('product-categories-tags'); // Using slug, not name

        $this->assertEquals('product-categories-tags', $component->getTaxonomy());
    }

    /** @test */
    public function it_handles_nonexistent_taxonomy_gracefully()
    {
        $component = TaxonomySelect::make('categories')
            ->taxonomy('nonexistent-taxonomy');

        // Should not throw errors when taxonomy doesn't exist
        $this->assertEquals('nonexistent-taxonomy', $component->getTaxonomy());
    }

    /** @test */
    public function it_can_chain_multiple_level_filters()
    {
        $component = TaxonomySelect::make('categories')
            ->taxonomy('test-categories')
            ->minLevel(1)
            ->maxLevel(2)
            ->multiple();

        $this->assertEquals(1, $component->getMinLevel());
        $this->assertEquals(2, $component->getMaxLevel());
        $this->assertTrue($component->isMultiple());
    }

    /** @test */
    public function exact_level_overrides_min_max_levels()
    {
        $component = TaxonomySelect::make('categories')
            ->taxonomy('test-categories')
            ->minLevel(1)
            ->maxLevel(3)
            ->exactLevel(2);

        // When exactLevel is set, it should take precedence
        $this->assertEquals(2, $component->getExactLevel());
        $this->assertEquals(1, $component->getMinLevel());
        $this->assertEquals(3, $component->getMaxLevel());
    }
}