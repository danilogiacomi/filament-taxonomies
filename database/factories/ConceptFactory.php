<?php

namespace Net7\FilamentTaxonomies\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ConceptFactory extends Factory
{

    protected $model = \Net7\FilamentTaxonomies\Models\Concept::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->unique()->name(),
            'definition' => $this->faker->paragraph(),
            // 'exact_match' => $this->faker->word(),
            'order_column' => $this->faker->unique()->randomDigit(),
            'concept_schema_id' => $this->faker->randomDigitNotNull(),
            'uri' => $this->faker->url()

        ];
    }
}
