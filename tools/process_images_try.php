<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use App\Services\Image\ImageProcessor;

$disk = 'public';
$sourceDir = 'images_try';
$destDir = 'universities'; // change to 'colleges' if needed

/** @var ImageProcessor $processor */
$processor = app(ImageProcessor::class);

$all = Storage::disk($disk)->files($sourceDir);

$images = array_values(array_filter($all, function ($p) {
    $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg','jpeg','png','webp','gif','heic']);
}));

if (empty($images)) {
    echo "No images found in $sourceDir on disk '$disk'.\n";
    exit(0);
}

$total = count($images);
$ok = 0; $fail = 0;
$results = [];

echo "Processing $total image(s) from $sourceDir -> $destDir ...\n";

foreach ($images as $i => $path) {
    try {
        $res = $processor->reprocessFromDisk($disk, $path, $destDir, deleteOriginal: false);
        if (is_array($res) && !empty($res['path_main'])) {
            $ok++;
            $results[] = [
                'src' => $path,
                'main' => $res['path_main'],
                'thumb' => $res['path_thumb'] ?? null,
            ];
            echo sprintf("[%d/%d] OK: %s -> %s\n", $i+1, $total, $path, $res['path_main']);
        } else {
            $fail++;
            echo sprintf("[%d/%d] FAIL: %s (no result)\n", $i+1, $total, $path);
        }
    } catch (Throwable $e) {
        $fail++;
        echo sprintf("[%d/%d] ERROR: %s (%s)\n", $i+1, $total, $path, $e->getMessage());
    }
}

// Save a small report to storage/app/public for inspection
$reportPath = 'images_try_report.json';
Storage::disk($disk)->put($reportPath, json_encode([
    'source' => $sourceDir,
    'destination' => $destDir,
    'total' => $total,
    'ok' => $ok,
    'fail' => $fail,
    'items' => $results,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo "Done. OK=$ok, FAIL=$fail. Report: public/$reportPath\n";
