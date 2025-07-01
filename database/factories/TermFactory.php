<?php

namespace Net7\FilamentTaxonomies\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Net7\FilamentTaxonomies\Models\Term;

class TermFactory extends Factory
{
    protected $model = Term::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'parent_id' => null,
            'uri' => $this->faker->url(),
            'exact_match_uri' => $this->faker->optional(0.3)->url(),
            'uri_type' => \Net7\FilamentTaxonomies\Enums\UriTypes::internal->value,
        ];
    }

    public function withParent(Term $parent = null): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent ? $parent->id : Term::factory()->create()->id,
        ]);
    }

    public function rootLevel(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    public function withExactMatch(): static
    {
        return $this->state(fn (array $attributes) => [
            'exact_match_uri' => $this->faker->url(),
        ]);
    }
}