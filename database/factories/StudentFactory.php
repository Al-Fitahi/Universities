<?php

namespace Database\Factories;

use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $digits = $this->faker->numerify('0#########');

        return [
            'F_name' => $this->faker->firstName(),
            'S_name' => $this->faker->lastName(),
            'Th_name' => $this->faker->firstName(),
            'Su_name' => $this->faker->lastName(),
            'phone_number' => $digits,
            'graduation_date' => $this->faker->date(),
            // float(2,2) in migration => 0.00 to 0.99
            'graduation_grade' => $this->faker->randomFloat(2, 0, 0.99),
            'certificate_image' => self::randomProcessedImage(),
            // 'project_id' => ProjectType::inRandomOrder()->first()->id,
        ];
    }

    protected static function randomProcessedImage(): ?string
    {
        static $images = null;
        if ($images === null) {
            $all = Storage::disk('public')->files('universities');
            $images = array_values(array_filter($all, function ($p) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                return in_array($ext, ['webp']);
            }));
        }

        return !empty($images) ? Arr::random($images) : null;
    }
}
