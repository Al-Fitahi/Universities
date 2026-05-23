<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\University;
use Illuminate\Support\Facades\Storage;

$u = University::first();
if (! $u) {
    echo "No university records found.\n";
    exit;
}

echo "University id/public_id: " . ($u->id ?? 'NULL') . " / " . ($u->public_id ?? 'NULL') . PHP_EOL;
echo "image_path (db): " . ($u->image_path ?? 'NULL') . PHP_EOL;

$state = $u->image_path;
if (! $state) {
    echo "No image_path set on model.\n";
    exit;
}

$exists = Storage::disk('public')->exists($state) ? 'YES' : 'NO';
$publicDiskConfig = config('filesystems.disks.public', []);
$baseUrl = $publicDiskConfig['url'] ?? null;

if ($baseUrl) {
    $url = rtrim($baseUrl, '/') . '/' . ltrim($state, '/');
} else {
    // Fallback: build a URL pointing to the public/storage symlink
    $url = asset('storage/' . ltrim($state, '/'));
}

echo "File exists on public disk: $exists\n";
echo "Public URL: $url\n";

// Show physical path
$path = Storage::disk('public')->path($state);
echo "Physical path: $path\n";

// List directory
$dir = dirname($path);
if (is_dir($dir)) {
    echo "Files in directory ($dir):\n";
    $files = array_slice(scandir($dir), 0, 200);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        echo " - $f\n";
    }
} else {
    echo "Directory not found: $dir\n";
}
