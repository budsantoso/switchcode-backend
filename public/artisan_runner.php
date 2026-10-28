<?php
// artisan_runner.php - Menjalankan migrasi & seed di server cPanel
$secret = 'SwitchCodeDeploy2026SecretKey';

if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403);
    die('Unauthorized');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$cmd = $_GET['cmd'] ?? 'migrate:fresh --seed';

echo "<pre>";
echo "Running: php artisan {$cmd}\n\n";

try {
    Artisan::call($cmd, ['--force' => true]);
    echo Artisan::output();
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}

echo "</pre>";
