<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\College>
 */
class CollegeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' College',
            // keep under varchar(255)
            'description' => $this->faker->sentence(12),
            'image_path' => self::randomProcessedImage(),
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
