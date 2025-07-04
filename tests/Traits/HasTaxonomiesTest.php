<?php

namespace Net7\FilamentTaxonomies\Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Tests\TestCase;
use Net7\FilamentTaxonomies\Traits\HasTaxonomies;

class HasTaxonomiesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create test model table
        $this->app['db']->connection()->getSchemaBuilder()->create('test_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /** @test */
    public function it_can_set_and_get_terms_by_taxonomy_id()
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term1 = Term::create(['name' => 'Term 1']);
        $term2 = Term::create(['name' => 'Term 2']);

        $model->setTermsForTaxonomyId($taxonomy->id, [$term1->id, $term2->id]);

        $retrievedTerms = $model->getTermsForTaxonomyId($taxonomy->id);

        $this->assertCount(2, $retrievedTerms);
        $this->assertTrue($retrievedTerms->contains('name', 'Term 1'));
        $this->assertTrue($retrievedTerms->contains('name', 'Term 2'));
    }

    /** @test */
    public function it_can_set_and_get_terms_by_taxonomy_slug()
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $taxonomy = Taxonomy::create([
            'name' => 'Product Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term1 = Term::create(['name' => 'Term 1']);
        $term2 = Term::create(['name' => 'Term 2']);

        $model->setTermsForTaxonomySlug('product-categories', [$term1->id, $term2->id]);

        $retrievedTerms = $model->getTermsForTaxonomySlug('product-categories');

        $this->assertCount(2, $retrievedTerms);
        $this->assertTrue($retrievedTerms->contains('name', 'Term 1'));
        $this->assertTrue($retrievedTerms->contains('name', 'Term 2'));
    }

    /** @test */
    public function it_can_check_if_entity_has_term_in_taxonomy()
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        $model->setTermsForTaxonomyId($taxonomy->id, [$term->id]);

        $this->assertTrue($model->hasTermInTaxonomyId($taxonomy->id, $term->id));
        $this->assertFalse($model->hasTermInTaxonomyId($taxonomy->id, 999));
    }

    /** @test */
    public function it_replaces_terms_when_setting_new_ones()
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term1 = Term::create(['name' => 'Term 1']);
        $term2 = Term::create(['name' => 'Term 2']);
        $term3 = Term::create(['name' => 'Term 3']);

        // Set initial terms
        $model->setTermsForTaxonomyId($taxonomy->id, [$term1->id, $term2->id]);
        $this->assertCount(2, $model->getTermsForTaxonomyId($taxonomy->id));

        // Replace with new terms
        $model->setTermsForTaxonomyId($taxonomy->id, [$term3->id]);
        $retrievedTerms = $model->getTermsForTaxonomyId($taxonomy->id);

        $this->assertCount(1, $retrievedTerms);
        $this->assertTrue($retrievedTerms->contains('name', 'Term 3'));
        $this->assertFalse($retrievedTerms->contains('name', 'Term 1'));
    }

    /** @test */
    public function it_automatically_deletes_entity_terms_when_model_is_deleted()
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        $model->setTermsForTaxonomyId($taxonomy->id, [$term->id]);

        // Verify entity term exists
        $this->assertDatabaseHas('entity_terms', [
            'entity_type' => TestModel::class,
            'entity_id' => $model->id,
            'taxonomy_id' => $taxonomy->id,
            'term_id' => $term->id,
        ]);

        // Delete the model
        $model->delete();

        // Verify entity terms are cleaned up
        $this->assertDatabaseMissing('entity_terms', [
            'entity_type' => TestModel::class,
            'entity_id' => $model->id,
        ]);
    }

    /** @test */
    public function legacy_methods_still_work_with_taxonomy_names()
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $taxonomy = Taxonomy::create([
            'name' => 'Product Categories',
            'state' => TaxonomyStates::published,
            'type' => TaxonomyTypes::public,
        ]);

        $term = Term::create(['name' => 'Test Term']);

        // Test legacy name-based methods
        $model->setTermsForTaxonomy('Product Categories', [$term->id]);
        $retrievedTerms = $model->getTermsForTaxonomy('Product Categories');

        $this->assertCount(1, $retrievedTerms);
        $this->assertTrue($retrievedTerms->contains('name', 'Test Term'));
        $this->assertTrue($model->hasTermInTaxonomy('Product Categories', $term->id));
    }

    /** @test */
    public function it_handles_nonexistent_taxonomies_gracefully()
    {
        $model = TestModel::create(['name' => 'Test Model']);

        // Should not throw errors
        $model->setTermsForTaxonomySlug('nonexistent-taxonomy', [1, 2, 3]);
        $terms = $model->getTermsForTaxonomySlug('nonexistent-taxonomy');
        $hasTerms = $model->hasTermInTaxonomySlug('nonexistent-taxonomy', 1);

        $this->assertCount(0, $terms);
        $this->assertFalse($hasTerms);
    }
}

class TestModel extends Model
{
    use HasTaxonomies;

    protected $fillable = ['name'];

    protected $table = 'test_models';
}
