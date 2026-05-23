<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Optimizers
    |--------------------------------------------------------------------------
    |
    | ضبط خيارات الضغط إلى أقصى درجة عملية دون تدمير الجودة. تأكد من توفر الأدوات
    | في الخادم: cwebp/jpegoptim/pngquant/optipng/gifsicle/svgo/avifenc.
    |
    */

    'optimizers' => [
        Spatie\ImageOptimizer\Optimizers\Cwebp::class => [
            '-m', '6', // method (0-6)
            '-q', '80',
            '-mt',      // multi-thread
            '-af',      // auto-filter
        ],

        Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '--strip-all',
            '--all-progressive',
            '--max=80',
        ],

        Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force',
            '--strip',
            '--skip-if-larger',
            '--quality=70-85',
            '--speed=1',
        ],

        Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-o7',
            '-strip', 'all',
        ],

        Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
            '-b',
            '-O3',
        ],

        Spatie\ImageOptimizer\Optimizers\Svgo::class => [
            '--disable=cleanupIDs',
        ],
    ],

    'binary_path' => null,

    'timeout' => 60,
];
