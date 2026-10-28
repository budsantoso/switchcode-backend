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

$cmd = $_GET['cmd'] ?? 'migrate';

echo "<pre>";
echo "Running Artisan command: {$cmd}\n\n";

try {
    if ($cmd === 'migrate:fresh' || $cmd === 'migrate:fresh --seed') {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
    } elseif ($cmd === 'storage:link') {
        $target = __DIR__ . '/../storage/app/public';
        $link = __DIR__ . '/storage';
        if (!file_exists($link)) {
            @symlink($target, $link);
        }
        echo "Storage symlink created manually: " . (is_link($link) ? 'SUCCESS' : 'FAILED') . "\n";
    } else {
        Artisan::call($cmd, ['--force' => true]);
    }
    echo Artisan::output();
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}

echo "</pre>";
