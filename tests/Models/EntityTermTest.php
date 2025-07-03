<?php

namespace Net7\FilamentTaxonomies\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Net7\FilamentTaxonomies\Models\EntityTerm;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Traits\HasTaxonomies;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;
use Net7\FilamentTaxonomies\Tests\TestCase;

class EntityTermTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test model table
        $this->app['db']->connection()->getSchemaBuilder()->create('test_entities', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /** @test */
    public function it_can_create_entity_term_relationship()
    {
        $entity = TestEntity::create(['name' => 'Test Entity']);
        
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        $entityTerm = EntityTerm::create([
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);

        $this->assertDatabaseHas('entity_terms', [
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);
    }

    /** @test */
    public function it_belongs_to_entity_polymorphically()
    {
        $entity = TestEntity::create(['name' => 'Test Entity']);
        
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        $entityTerm = EntityTerm::create([
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);

        $this->assertInstanceOf(TestEntity::class, $entityTerm->entity);
        $this->assertEquals($entity->id, $entityTerm->entity->id);
    }

    /** @test */
    public function it_belongs_to_term()
    {
        $entity = TestEntity::create(['name' => 'Test Entity']);
        
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        $entityTerm = EntityTerm::create([
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);

        $this->assertInstanceOf(Term::class, $entityTerm->term);
        $this->assertEquals($term->id, $entityTerm->term->id);
    }

    /** @test */
    public function it_belongs_to_taxonomy()
    {
        $entity = TestEntity::create(['name' => 'Test Entity']);
        
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        $entityTerm = EntityTerm::create([
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);

        $this->assertInstanceOf(Taxonomy::class, $entityTerm->taxonomy);
        $this->assertEquals($taxonomy->id, $entityTerm->taxonomy->id);
    }

    /** @test */
    public function it_enforces_unique_constraint()
    {
        $entity = TestEntity::create(['name' => 'Test Entity']);
        
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        // Create first entity term
        EntityTerm::create([
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);

        // Attempt to create duplicate should fail
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        EntityTerm::create([
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);
    }

    /** @test */
    public function it_cascades_delete_when_taxonomy_is_deleted()
    {
        $entity = TestEntity::create(['name' => 'Test Entity']);
        
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        $entityTerm = EntityTerm::create([
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);

        // Delete taxonomy
        $taxonomy->delete();

        // Entity term should be deleted due to foreign key constraint
        $this->assertDatabaseMissing('entity_terms', [
            'id' => $entityTerm->id,
        ]);
    }

    /** @test */
    public function it_cascades_delete_when_term_is_deleted()
    {
        $entity = TestEntity::create(['name' => 'Test Entity']);
        
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        $entityTerm = EntityTerm::create([
            'entity_type' => TestEntity::class,
            'entity_id' => $entity->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);

        // Delete term
        $term->delete();

        // Entity term should be deleted due to foreign key constraint
        $this->assertDatabaseMissing('entity_terms', [
            'id' => $entityTerm->id,
        ]);
    }
}

class TestEntity extends Model
{
    use HasTaxonomies;

    protected $fillable = ['name'];
    protected $table = 'test_entities';
}