<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;

echo "Starting student create/update test...\n";

$s = Student::findOrCreateByFullName([
    'F_name' => 'TestF',
    'S_name' => 'TestS',
    'Th_name' => 'TestT',
    'Su_name' => 'TestU',
    'phone_number' => '111',
    'graduation_date' => '2020-01-01',
    'graduation_grade' => 90,
    'certificate_image' => 'img1',
]);

echo "FIRST: " . json_encode($s->toArray()) . PHP_EOL;

$s2 = Student::findOrCreateByFullName([
    'F_name' => 'TestF',
    'S_name' => 'TestS',
    'Th_name' => 'TestT',
    'Su_name' => 'TestU',
    'phone_number' => '222',
    'graduation_date' => '2021-02-02',
    'graduation_grade' => 95,
    'certificate_image' => 'img2',
]);

echo "SECOND: " . json_encode($s2->toArray()) . PHP_EOL;

echo "Done.\n";
