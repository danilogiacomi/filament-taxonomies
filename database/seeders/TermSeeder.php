<?php

namespace Net7\FilamentTaxonomies\Database\Seeders;

use Illuminate\Database\Seeder;
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Models\Term;

class TermSeeder extends Seeder
{
    public function run(): void
    {
        $taxonomies = Taxonomy::all();

        if ($taxonomies->isEmpty()) {
            $taxonomies = Taxonomy::factory()->count(3)->create();
        }

        $rootTerms = Term::factory()
            ->count(15)
            ->rootLevel()
            ->create();

        $childTerms = Term::factory()
            ->count(20)
            ->create([
                'parent_id' => $rootTerms->random()->id,
            ]);

        Term::factory()
            ->withExactMatch()
            ->create([
                'name' => 'Sample Term with Exact Match',
                'description' => 'A sample term that has an exact match URI',
                'parent_id' => $rootTerms->first()->id,
            ]);

        foreach ($taxonomies as $taxonomy) {
            $termsToAttach = $rootTerms->random(rand(2, 5));
            $taxonomy->terms()->attach($termsToAttach);
        }

        foreach ($taxonomies as $taxonomy) {
            $childTermsToAttach = $childTerms->random(rand(3, 8));
            $taxonomy->terms()->attach($childTermsToAttach);
        }
    }
}
