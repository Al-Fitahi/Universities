<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\College;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Major>
 */
class MajorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraph(),
            'designation_jobs' => implode(', ', $this->faker->words(3)),
            'study_years' => $this->faker->numberBetween(3, 6),
            'college_id' => College::factory(),
        ];
    }
}
