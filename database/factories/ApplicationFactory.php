<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
use App\Models\UniversityMajor;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'application_code' => strtoupper($this->faker->bothify('APP-#####')),
            'university_major_id' => UniversityMajor::factory(),
            'user_id' => User::factory(),
            'status' => \App\Models\Application::STATUS_PROCESSING,
            'is_active' => true,
        ];
    }
}
