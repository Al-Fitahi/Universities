<?php

return [
    // Disable this if VPS image stack cannot safely convert uploaded profiles.
    'colorspace' => [
        'enabled' => env('IMAGE_COLORSPACE_ENABLED', true),
        'target' => env('IMAGE_COLORSPACE_TARGET', 'srgb'),
        'allowed_drivers' => array_filter(array_map(
            static fn (string $driver): string => trim($driver),
            explode(',', env('IMAGE_COLORSPACE_ALLOWED_DRIVERS', 'imagick'))
        )),
    ],
];
