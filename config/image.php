<?php

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

$driver = strtolower((string) env('IMAGE_DRIVER', 'gd'));
$driverClass = $driver === 'imagick' ? ImagickDriver::class : GdDriver::class;

return [
    'driver' => $driverClass,

    'options' => [
        'autoOrientation' => true,
        'decodeAnimation' => true,
        'blendingColor' => 'ffffff',
        'strip' => false,
    ],
];
