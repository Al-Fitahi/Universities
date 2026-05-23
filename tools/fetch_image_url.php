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
    exit(1);
}

$path = $u->image_path;
if (! $path) {
    echo "No image_path in DB.\n";
    exit(1);
}

// Use the unlocked route URL for testing CORS headers
$url = (string) url('/storage-unlocked/' . ltrim($path, '/'));
echo "Checking URL: $url\n";

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: PHP-Check/1.0\r\n",
        "timeout" => 10,
    ]
];
$context = stream_context_create($opts);
$start = microtime(true);
$res = @file_get_contents($url, false, $context);
$time = round((microtime(true) - $start) * 1000);

if ($res === false) {
    echo "GET failed (no body).\n";
    if (! empty($http_response_header)) {
        echo "Response headers:\n";
        foreach ($http_response_header as $h) {
            echo " - $h\n";
        }
    }
    exit(1);
}

echo "GET succeeded, byte length: " . strlen($res) . " (" . $time . "ms)\n";
if (! empty($http_response_header)) {
    echo "Response headers:\n";
    foreach ($http_response_header as $h) {
        echo " - $h\n";
    }
}

// Save a temp copy to confirm image data
$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($path);
file_put_contents($tmp, $res);
echo "Saved preview to: $tmp\n";
