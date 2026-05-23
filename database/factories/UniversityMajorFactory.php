<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Major;
use App\Models\University;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\University_Major>
 */
class UniversityMajorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number_of_seats' => $this->faker->numberBetween(30, 300),
            'admission_rate' => $this->faker->numberBetween(50, 95),
            'study_years' => $this->faker->numberBetween(3, 6),
            'tuition_fee' => $this->faker->numberBetween(500, 5000),
            'major_id' => Major::factory(),
            'university_id' => University::factory(),
            'published' => true,
        ];
    }
}
