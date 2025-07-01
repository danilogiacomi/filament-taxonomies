<?php

namespace Net7\FilamentTaxonomies\Database\Seeders;

use Illuminate\Database\Seeder;
use Net7\FilamentTaxonomies\Models\Taxonomy;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        Taxonomy::factory()->count(10)->create();

        Taxonomy::factory()
            ->published()
            ->public()
            ->create([
                'name' => 'Sample Public Taxonomy',
                'description' => 'A sample public taxonomy for demonstration purposes',
            ]);

        Taxonomy::factory()
            ->working()
            ->restricted()
            ->create([
                'name' => 'Work in Progress Taxonomy',
                'description' => 'A taxonomy that is currently being worked on',
            ]);

        Taxonomy::factory()
            ->published()
            ->private()
            ->create([
                'name' => 'Private Internal Taxonomy',
                'description' => 'An internal taxonomy for private use',
            ]);
    }
}