<?php

namespace Net7\FilamentTaxonomies\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Net7\FilamentTaxonomies\Enums\ConceptSchemaStates;
use Net7\FilamentTaxonomies\Enums\ConceptSchemaTypes;
use Nette\Utils\Random;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ConceptSchemaFactory extends Factory
{

    protected $model = \Net7\FilamentTaxonomies\Models\ConceptSchema::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'state' => $this->faker->randomElement(ConceptSchemaStates::names()),
            'type' => $this->faker->randomElement(ConceptSchemaTypes::names()),
            'uri' => $this->faker->url(),
            'owner' => $this->faker->word(),
            'creator' => $this->faker->word(),
            'license' => $this->faker->sentence(),

        ];
    }
}
