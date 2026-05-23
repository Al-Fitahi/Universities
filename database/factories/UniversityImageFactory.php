<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use App\Models\University;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UniversityImage>
 */
class UniversityImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        [$main, $thumb] = self::randomProcessedPair();

        return [
            'university_id' => University::factory(),
            // store the relative path within the public disk
            'path_main' => $main,
            'path_thumb' => $thumb,
            'priority' => $this->faker->numberBetween(0, 5),
            'is_active' => true,
        ];
    }

    protected static function randomProcessedPair(): array
    {
        static $mains = null;
        if ($mains === null) {
            $all = Storage::disk('public')->files('universities');
            $mains = array_values(array_filter($all, function ($p) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                return str_ends_with(strtolower($p), '.webp') && !str_contains($p, '_thumb');
            }));
        }

        if (empty($mains)) {
            return ['universities/sample.webp', 'universities/sample_thumb.webp'];
        }

        $main = Arr::random($mains);
        $thumb = preg_replace('/\.webp$/i', '_thumb.webp', $main);
        if (! Storage::disk('public')->exists($thumb)) {
            $thumb = $main;
        }
        return [$main, $thumb];
    }
}
