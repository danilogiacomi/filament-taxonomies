<?php

namespace Net7\FilamentTaxonomies\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;
use Net7\FilamentTaxonomies\Models\Taxonomy;

class TaxonomyFactory extends Factory
{
    protected $model = Taxonomy::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->sentence(),
            'state' => $this->faker->randomElement(TaxonomyStates::cases())->value,
            'type' => $this->faker->randomElement(TaxonomyTypes::cases())->value,
        ];
    }

    public function working(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => TaxonomyStates::working->value,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => TaxonomyStates::published->value,
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TaxonomyTypes::public->value,
        ]);
    }

    public function restricted(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TaxonomyTypes::restricted->value,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TaxonomyTypes::private->value,
        ]);
    }
}
